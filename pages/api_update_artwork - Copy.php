<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';
if(!isset($_SESSION['umjetnik_id'])){ echo json_encode(['ok'=>false,'error'=>'Unauthorized']); exit; }
$uid=(int)$_SESSION['umjetnik_id'];
$id=(int)($_POST['id']??0); $naslov=trim($_POST['naslov']??''); $opis=trim($_POST['opis']??''); $cijena=(float)($_POST['cijena']??0);
if($id<=0||!$naslov||$cijena<=0){ echo json_encode(['ok'=>false,'error'=>'Neispravan unos']); exit; }
$chk=$conn->prepare("SELECT slika FROM umjetnine WHERE id=? AND umjetnik_id=? LIMIT 1"); $chk->bind_param('ii',$id,$uid); $chk->execute(); $own=$chk->get_result()->fetch_assoc();
if(!$own){ echo json_encode(['ok'=>false,'error'=>'Zabranjeno']); exit; }
$newFile=$own['slika'];
if(isset($_FILES['slika']) && $_FILES['slika']['error']===UPLOAD_ERR_OK){
  $ext=strtolower(pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION));
  if(!in_array($ext,['jpg','jpeg','png','webp'])){ echo json_encode(['ok'=>false,'error':'Format slike nije dozvoljen']); exit; }
  $name=uniqid('art_').'.'.$ext; $dest=__DIR__.'/../uploads/'.$name;
  if(!move_uploaded_file($_FILES['slika']['tmp_name'],$dest)){ echo json_encode(['ok'=>false,'error'=>'Upload nije uspio']); exit; }
  $newFile=$name;
}
$upd=$conn->prepare("UPDATE umjetnine SET naslov=?, opis=?, cijena=?, slika=? WHERE id=? AND umjetnik_id=?");
$upd->bind_param('ssdsii',$naslov,$opis,$cijena,$newFile,$id,$uid);
if($upd->execute()) echo json_encode(['ok'=>true]); else echo json_encode(['ok'=>false,'error'=>'DB greška']);
