<?php

// Only the group-submission form POSTs here. Reject everything else before
// loading the framework so a bare GET can't drive this endpoint at all.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode('Method Not Allowed');
    exit;
}

require '../vendor/autoload.php';
require __DIR__.'/../src/submission.php';

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Mailgun\Mailgun;

// 5 accepted POSTs per 10 minutes per IP, keyed by client IP.
// X-Forwarded-For is trusted because all traffic enters through Traefik.
$factory = new RateLimiterFactory(
    [
        'id'       => 'mail_submission',
        'policy'   => 'sliding_window',
        'limit'    => 5,
        'interval' => '10 minutes',
    ],
    new CacheStorage(new FilesystemAdapter('arm-mail-ratelimit', 0, sys_get_temp_dir()))
);

$limit = $factory->create(arm_client_ip($_SERVER))->consume();

if (!$limit->isAccepted()) {
    http_response_code(429);
    header('Retry-After: '.max(0, $limit->getRetryAfter()->getTimestamp() - time()));
    echo json_encode('Too Many Requests');
    exit;
}

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
    $fields = arm_validate_submission($_POST);

    if ($fields === null) {
        http_response_code(400);
        echo json_encode('Invalid submission');
        exit;
    }

    $mg = Mailgun::create($_ENV['MAILGUN_API_KEY'], 'https://api.eu.mailgun.net');

    $mg->messages()->send('animalrightsmap.org', [
        'from'       => 'Animal Rights Map <noreply@animalrightsmap.org>',
        'to'         => 'map@veganhacktivists.org',
        'h:Reply-To' => $fields['email'],
        'subject'    => 'New Group Submission',
        'html'       => arm_submission_html($fields),
    ]);

    echo json_encode('OK');

} catch (\Throwable $exception) {
    // Log detail server-side; never leak it to the client.
    error_log('mail.php: '.$exception->getMessage());
    http_response_code(500);
    echo json_encode('Error');
}
