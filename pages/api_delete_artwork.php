<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = (int)$_POST['id'];
  $conn->query("DELETE FROM umjetnine WHERE id=$id");
}
header('Location: dodaj_umjetninu.php');
exit;
?>
