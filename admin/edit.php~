<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['user'])) { http_response_code(403); exit('Forbidden'); }

$t = $_POST['t'] ?? ''; $pk = $_POST['pk'] ?? ''; $id = $_POST['id'] ?? null;
if (!$t || !$pk || $id===null) { exit('Invalid'); }

$sql = "DELETE FROM `{$t}` WHERE `{$pk}`=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $id);
$stmt->execute();

header('Location: table_view.php?t='.urlencode($t));
