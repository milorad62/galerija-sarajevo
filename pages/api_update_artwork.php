<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['umjetnik_id'])) {
  http_response_code(403);
  echo json_encode(['error' => 'Niste prijavljeni.']);
  exit;
}

$id = (int)($_POST['id'] ?? 0);
$uid = (int)$_SESSION['umjetnik_id'];
$naslov = trim($_POST['naslov'] ?? '');
$opis = trim($_POST['opis'] ?? '');
$cijena = (float)($_POST['cijena'] ?? 0);

if ($id <= 0 || $naslov === '' || $opis === '' || $cijena <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Neispravni podaci.']);
  exit;
}

$stmt = $conn->prepare("UPDATE umjetnine SET naslov=?, opis=?, cijena=? WHERE id=? AND umjetnik_id=?");
$stmt->bind_param('ssdii', $naslov, $opis, $cijena, $id, $uid);
if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Greška pri ažuriranju.']);
}
