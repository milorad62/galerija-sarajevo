<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['user'])) { http_response_code(403); exit('Forbidden'); }

$t = $_POST['t'] ?? ''; $pk = $_POST['pk'] ?? ''; $id = $_POST['id'] ?? null; $field = $_POST['field'] ?? ''; $value = $_POST['value'] ?? '';
if (!$t || !$pk || $id===null || !$field) { exit('Invalid'); }

$sql = "UPDATE `{$t}` SET `{$field}`=? WHERE `{$pk}`=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $value, $id);
$stmt->execute();

header('Location: table_view.php?t='.urlencode($t));
