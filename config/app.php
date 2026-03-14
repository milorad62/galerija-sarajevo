<?php
// Produkcijske postavke (komercijalna verzija)
return [
  'app_env' => getenv('APP_ENV') ?: 'production', // production|development
  'site_email_from' => 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'example.com'),
  'site_name' => 'MojaGalerija.ba',
  'contact_to' => (function() {
      $emails = require __DIR__ . '/../emails.php';
      return $emails['kontakt'] ?? 'kontakt@tvoja-domena.com';
  })(),
];
