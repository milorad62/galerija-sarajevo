<?php
require __DIR__ . '/config/bootstrap.php';

// uništi session
$_SESSION = [];
session_destroy();

// 🔥 redirect + refresh
header("Location: /?logout=1");
exit;
