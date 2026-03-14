<?php
$config = require __DIR__ . '/app.php';

if (($config['app_env'] ?? 'production') === 'development') {
  ini_set('display_errors', '1');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', '0');
  ini_set('log_errors', '1');
  // log u /storage/logs/app.log (ako postoji)
  $logDir = __DIR__ . '/../storage/logs';
  if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
  ini_set('error_log', $logDir . '/app.log');
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}


// Base URL (radi i u podfolderu i na root domeni)
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$BASE_URL = rtrim(str_replace('\\', '/', dirname($script)), '/');
if ($BASE_URL === '/') { $BASE_URL = ''; }
