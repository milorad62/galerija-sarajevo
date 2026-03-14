<?php
require __DIR__ . '/config/bootstrap.php';

session_unset();
session_destroy();
header("Location: /galerija_sarajevo/login.php");
exit;
?>

