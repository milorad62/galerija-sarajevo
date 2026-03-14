<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION = []; session_destroy();
header('Location: /galerija_sarajevo/login.php');
