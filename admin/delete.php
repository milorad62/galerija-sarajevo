<?php
require_once __DIR__ . '/../db.php';
$tabela = $_GET['tabela'] ?? '';
$id = (int)($_GET['id'] ?? 0);
if ($tabela && $id) {
  $conn->query("DELETE FROM $tabela WHERE id=$id");
  header("Location: index.php?tabela=$tabela");
  exit;
}
echo "Neispravan zahtjev.";
?>
