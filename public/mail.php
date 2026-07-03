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

use Symfony\Component\Dotenv\Dotenv;
use Mailgun\Mailgun;

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
