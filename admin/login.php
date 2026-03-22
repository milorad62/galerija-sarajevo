<?php
require __DIR__ . '/../config/bootstrap.php';

$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

header('Location: ' . $BASE_URL . '/login.php?redirect=' . urlencode($BASE_URL . '/admin/'));
exit;
