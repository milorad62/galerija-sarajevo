<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';
if(!isset($_SESSION['umjetnik_id'])){ echo json_encode(['ok'=>false,'error'=>'Unauthorized']); exit; }
$uid=(int)$_SESSION['umjetnik_id']; $id=(int)($_POST['id']??0);
if($id<=0){ echo json_encode(['ok'=>false,'error'=>'Neispravan ID']); exit; }
$del=$conn->prepare("DELETE FROM umjetnine WHERE id=? AND umjetnik_id=? LIMIT 1"); $del->bind_param('ii',$id,$uid);
if($del->execute()) echo json_encode(['ok'=>true]); else echo json_encode(['ok'=>false,'error'=>'DB greška']);
