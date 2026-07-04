<?php

use Symfony\Component\Dotenv\Dotenv;
use Mailgun\Mailgun;

// Per-IP submission limits: at most MAIL_RATE_MAX_REQUESTS accepted POSTs per
// MAIL_RATE_WINDOW_SECONDS. Generous enough that a person submitting a few
// groups is never blocked, tight enough to stop a bot flooding the inbox.
const MAIL_RATE_MAX_REQUESTS   = 5;
const MAIL_RATE_WINDOW_SECONDS = 600;

/**
 * The address used to bucket the rate limit.
 *
 * REMOTE_ADDR is the only value a caller can't forge at the TCP level. If this
 * app is ever deployed behind a trusted proxy/load balancer, configure the
 * proxy to pass the real client IP through to REMOTE_ADDR, or read the proxy's
 * *verified* forwarded header here — never trust a raw X-Forwarded-For, since a
 * caller can set it to a random value to dodge the limit.
 */
function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Sliding-window per-IP rate limit, backed by a lock-guarded temp file.
 *
 * Fails open: if the store can't be read or written, the request is allowed
 * rather than blocking legitimate submissions.
 */
function rate_limit_exceeded(string $ip, int $maxRequests, int $windowSeconds): bool
{
    $dir = sys_get_temp_dir().'/arm-mail-ratelimit';
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        return false;
    }

    $handle = @fopen($dir.'/'.hash('sha256', $ip), 'c+');
    if ($handle === false) {
        return false;
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            return false;
        }

        $now = time();
        $raw = stream_get_contents($handle);
        $hits = $raw === '' ? [] : array_map('intval', explode(',', $raw));
        // Keep only hits still inside the window.
        $hits = array_values(array_filter($hits, static fn ($t) => $t > $now - $windowSeconds));

        if (count($hits) >= $maxRequests) {
            return true;
        }

        $hits[] = $now;
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, implode(',', $hits));

        return false;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

// Only the group-submission form POSTs here. Reject everything else before
// loading the framework so a bare GET can't drive this endpoint at all.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode('Method Not Allowed');
    exit;
}

if (rate_limit_exceeded(client_ip(), MAIL_RATE_MAX_REQUESTS, MAIL_RATE_WINDOW_SECONDS)) {
    http_response_code(429);
    header('Retry-After: '.MAIL_RATE_WINDOW_SECONDS);
    echo json_encode('Too Many Requests');
    exit;
}

require '../vendor/autoload.php';

try {
    // Load environmental variables (only needed locally)
    $envFilePath = __DIR__.'/../.env';
    if (file_exists($envFilePath)) {
        $dotenv = new Dotenv();
        $dotenv->load($envFilePath);
    }

    // Collect the submitted fields. The recipient, sender and subject are fixed
    // server-side: this endpoint can only ever deliver a group submission to us,
    // never relay arbitrary mail to arbitrary recipients.
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $groupNames       = trim($_POST['groupNames'] ?? '');
    $socialMediaLinks = trim($_POST['socialMediaLinks'] ?? '');
    $regions          = trim($_POST['regions'] ?? '');
    $message          = trim($_POST['message'] ?? '');

    $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    if ($name === '' || $validEmail === false || $groupNames === '' || $socialMediaLinks === '' || $regions === '') {
        http_response_code(400);
        echo json_encode('Invalid submission');
        exit;
    }

    // Escape every user-supplied value before placing it in the HTML body.
    $escape = fn ($value) => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    $html = '<b>Name</b>: '.$escape($name).'<br>'
        .'<b>Email</b>: '.$escape($validEmail).'<br>'
        .'<b>Group Name(s)</b>: '.$escape($groupNames).'<br>'
        .'<b>Social Media Link(s)</b>: '.$escape($socialMediaLinks).'<br>'
        .'<b>City/Region(s)</b>: '.$escape($regions).'<br>'
        .'<b>Message</b>: '.nl2br($escape($message)).'<br>';

    $mg = Mailgun::create($_ENV['MAILGUN_API_KEY'], 'https://api.eu.mailgun.net');

    $mg->messages()->send('animalrightsmap.org', [
        'from'       => 'Animal Rights Map <noreply@animalrightsmap.org>',
        'to'         => 'map@veganhacktivists.org',
        'h:Reply-To' => $validEmail,
        'subject'    => 'New Group Submission',
        'html'       => $html,
    ]);

    echo json_encode('OK');

} catch (\Exception $exception) {
    // Log detail server-side; never leak it to the client.
    error_log('mail.php: '.$exception->getMessage());
    http_response_code(500);
    echo json_encode('Error');
}
