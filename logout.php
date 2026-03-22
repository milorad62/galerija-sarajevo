<?php
require __DIR__ . '/config/bootstrap.php';

$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

// očisti session
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? true
    );
}

session_destroy();

// vrati na login
header('Location: ' . $BASE_URL . '/login.php');
exit;
