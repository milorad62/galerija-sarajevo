<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['umjetnik_id'])) { header('Location: /galerija_sarajevo/login.php'); exit; }
$uid=(int)$_SESSION['umjetnik_id']; $note=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['akcija']??'')==='dodaj'){
  $naslov=trim($_POST['naslov']??''); $opis=trim($_POST['opis']??''); $cijena=(float)($_POST['cijena']??0);
  if(!$naslov||$cijena<=0) $err='Naslov i cijena su obavezni i cijena mora biti > 0.';
  elseif(!isset($_FILES['slika'])||$_FILES['slika']['error']!==UPLOAD_ERR_OK) $err='Slika je obavezna.';
  else{
    $ext=strtolower(pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION));
    if(!in_array($ext,['jpg','jpeg','png','webp'])) $err='Dozvoljeni formati: jpg, jpeg, png, webp.';
    else{
      $name=uniqid('art_').'.'.$ext; $dest=__DIR__.'/../uploads/'.$name;
      if(move_uploaded_file($_FILES['slika']['tmp_name'],$dest)){
        $st=$conn->prepare("INSERT INTO umjetnine (naslov, opis, cijena, slika, umjetnik_id) VALUES (?,?,?,?,?)");
        $st->bind_param('ssdsi',$naslov,$opis,$cijena,$name,$uid);
        if($st->execute()) $note='Umjetnina dodana.'; else $err='Greška pri spremanju.';
      } else $err='Upload nije uspio.';
    }
  }
}
$my=$conn->prepare("SELECT * FROM umjetnine WHERE umjetnik_id=? ORDER BY id DESC"); $my->bind_param('i',$uid); $my->execute(); $list=$my->get_result();
?><!doctype html><html lang="bs"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Moje umjetnine — MojaGalerija</title>
<link rel="stylesheet" href="/galerija_sarajevo/assets/css/styles.css">
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head><body>
<div class="header"><strong>MojaGalerija.ba</strong><nav><a href="/galerija_sarajevo/">Početna</a><a href="/galerija_sarajevo/logout.php">Odjava</a></nav></div>
<div class="container">
  <div class="card" style="max-width:820px;margin:0 auto 1rem auto;">
    <h2>Dodaj novu umjetninu</h2>
    <?php if($note): ?><div class="badge"><?= htmlspecialchars($note) ?></div><?php endif; ?>
    <?php if($err): ?><div style="color:#b00;"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="akcija" value="dodaj">
      <label class="label">Naslov</label><input class="input" type="text" name="naslov" required>
      <label class="label">Opis</label><textarea class="input" name="opis" rows="4"></textarea>
      <label class="label">Cijena (npr. 199.99)</label><input class="input" type="number" step="0.01" min="0" name="cijena" required>
      <label class="label">Slika (JPG/PNG/WebP)</label><input class="input" type="file" name="slika" accept=".jpg,.jpeg,.png,.webp" required>
      <div style="margin-top:.8rem;"><button class="btn primary">Sačuvaj</button></div>
    </form>
  </div>
  <div class="card">
    <h2>Moje umjetnine</h2>
    <div class="grid">
      <?php while($row=$list->fetch_assoc()): ?>
      <div class="card">
        <img class="thumb" src="/galerija_sarajevo/uploads/<?= htmlspecialchars($row['slika']) ?>" alt="slika">
        <h3 style="margin:.6rem 0 0;"><?= htmlspecialchars($row['naslov']) ?></h3>
        <div class="note"><?= number_format((float)$row['cijena'],2,',','.') ?> KM</div>
        <div style="display:flex;gap:.5rem;margin-top:.6rem;">
          <button class="btn" onclick='openEditModal(<?= json_encode($row, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>)'>Uredi</button>
          <button class="btn danger" onclick="deleteArtwork(<?= (int)$row['id'] ?>)">Obriši</button>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<div id="modal" class="modal-backdrop">
  <div class="modal">
    <header><h3>Uredi umjetninu</h3><button class="btn" onclick="closeModal()">✕</button></header>
    <form id="editForm">
      <input type="hidden" name="id" id="e_id">
      <label class="label">Naslov</label><input class="input" type="text" name="naslov" id="e_naslov" required>
      <label class="label">Opis</label><textarea class="input" name="opis" id="e_opis" rows="4"></textarea>
      <label class="label">Cijena</label><input class="input" type="number" name="cijena" step="0.01" min="0" id="e_cijena" required>
      <label class="label">Nova slika (opcionalno)</label><input class="input" type="file" name="slika" id="e_slika" accept=".jpg,.jpeg,.png,.webp">
      <div class="actions"><button type="button" class="btn" onclick="closeModal()">Otkaži</button><button class="btn primary">Sačuvaj</button></div>
    </form>
  </div>
</div>

<script>
const modal=document.getElementById('modal');
function openEditModal(row){ modal.style.display='flex';
  document.getElementById('e_id').value=row.id;
  document.getElementById('e_naslov').value=row.naslov||'';
  document.getElementById('e_opis').value=row.opis||'';
  document.getElementById('e_cijena').value=row.cijena||'';
  document.getElementById('e_slika').value='';
}
function closeModal(){ modal.style.display='none'; }
document.getElementById('editForm').addEventListener('submit', async (e)=>{
  e.preventDefault(); const fd=new FormData(e.target);
  const rsp=await fetch('/galerija_sarajevo/pages/api_update_artwork.php',{method:'POST',body:fd});
  const data=await rsp.json(); if(data.ok) location.reload(); else alert(data.error||'Greška pri ažuriranju');
});
async function deleteArtwork(id){
  if(!confirm('Obrisati umjetninu?')) return;
  const fd=new FormData(); fd.append('id',id);
  const rsp=await fetch('/galerija_sarajevo/pages/api_delete_artwork.php',{method:'POST',body:fd});
  const data=await rsp.json(); if(data.ok) location.reload(); else alert(data.error||'Greška pri brisanju');
}
</script>
</body></html>
