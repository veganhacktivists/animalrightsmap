<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Mailgun\Mailgun;

// Load env variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mg = Mailgun::create(getenv('MAILGUN_API_KEY'));

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

