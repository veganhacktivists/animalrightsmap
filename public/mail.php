<?php
require '../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Mailgun\Mailgun;

// Load environmental variables (only needed locally)
$envFilePath = __DIR__ . '/../.env';
if (file_exists($envFilePath)) {
  $dotenv = new Dotenv();
  $dotenv->load($envFilePath);
}

$mg = Mailgun::create($_ENV['MAILGUN_API_KEY'], 'https://api.eu.mailgun.net');

try {
  $mg->messages()->send('animalrightsmap.org', [
    'from'    => $_GET['from'],
    'to'      => $_GET['to'],
    'subject' => $_GET['subject'],
    'html'    => $_GET['html']
  ]);

  echo json_encode("OK");

} catch (Mailgun\Exception\HttpClientException $exception) {
  print_r($exception);
}
