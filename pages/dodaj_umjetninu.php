<?php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['umjetnik_id'])) {
  header('Location: /galerija_sarajevo/login.php');
  exit;
}

$uid = (int)$_SESSION['umjetnik_id'];
$ime = htmlspecialchars($_SESSION['umjetnik_ime'] ?? 'Nepoznat umjetnik');

// Dodavanje nove umjetnine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_artwork'])) {
  $naslov = trim($_POST['naslov']);
  $opis = trim($_POST['opis']);
  $cijena = (float)$_POST['cijena'];
  $slika = '';

  if (!empty($_FILES['slika']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = time() . '_' . basename($_FILES['slika']['name']);
    $target = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['slika']['tmp_name'], $target)) {
      $slika = $fileName;
    }
  }

  $stmt = $conn->prepare("INSERT INTO umjetnine (umjetnik_id, naslov, opis, cijena, slika) VALUES (?,?,?,?,?)");
  $stmt->bind_param('issds', $uid, $naslov, $opis, $cijena, $slika);
  $stmt->execute();
  header('Location: dodaj_umjetninu.php');
  exit;
}
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dodaj umjetninu — MojaGalerija</title>
<link rel="stylesheet" href="./assets/styles.css">
</head>
<body>
<div class="header">
  <strong>Dobrodošli, <?= $ime ?></strong>
  <a href="/galerija_sarajevo/logout.php" class="btn danger">Odjava</a>
</div>

<div class="container">
  <h2>Moje umjetnine
    <button class="btn primary" id="openModalBtn">+ Dodaj novo djelo</button>
  </h2>

  <div class="grid">
  <?php
  $res = $conn->query("SELECT * FROM umjetnine WHERE umjetnik_id=$uid ORDER BY id DESC");
  if ($res && $res->num_rows > 0):
    while ($row = $res->fetch_assoc()):
      $sid = (int)$row['id'];
      $img = htmlspecialchars($row['slika']);
      $title = htmlspecialchars($row['naslov']);
      $price = number_format((float)$row['cijena'], 2);
  ?>
      <div class="card">
        <img class="thumb" src="/galerija_sarajevo/uploads/<?= $img ?>" alt="<?= $title ?>">
        <h4><?= $title ?></h4>
        <p><strong><?= $price ?> KM</strong></p>
        <div style="display:flex;gap:.5rem;">
          <form action="api_delete_artwork.php" method="post" onsubmit="return confirm('Obrisati ovo djelo?');">
            <input type="hidden" name="id" value="<?= $sid ?>">
            <button class="btn danger">Obriši</button>
          </form>
        </div>
      </div>
  <?php endwhile; else: ?>
    <p style="grid-column:1/-1;text-align:center;">Nema dodanih umjetnina.</p>
  <?php endif; ?>
  </div>
</div>

<!-- MODAL -->
<div id="modal-backdrop" class="modal-backdrop">
  <div class="modal">
    <header>
      <h3>Dodaj novo djelo</h3>
      <button class="btn closeModalBtn">✖</button>
    </header>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="add_artwork" value="1">

      <label class="label">Naslov</label>
      <input class="input" type="text" name="naslov" required>

      <label class="label">Opis</label>
      <textarea class="input" name="opis" rows="3" required></textarea>

      <label class="label">Cijena (KM)</label>
      <input class="input" type="number" name="cijena" step="0.01" required>

      <label class="label">Slika</label>
      <input class="input" type="file" name="slika" accept="image/*" required>

      <div class="actions">
        <button class="btn primary">Sačuvaj</button>
        <button type="button" class="btn closeModalBtn">Otkaži</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const backdrop = document.getElementById("modal-backdrop");
  const openBtn = document.getElementById("openModalBtn");
  const closeBtns = document.querySelectorAll(".closeModalBtn");
  console.log("JS učitan", openBtn, backdrop); // TEST LINIJA
  if (openBtn && backdrop) {
    openBtn.addEventListener("click", e => {
      e.preventDefault();
      backdrop.style.display = "flex";
    });
  }

  if (backdrop) {
    backdrop.addEventListener("click", e => {
      if (e.target.id === "modal-backdrop") backdrop.style.display = "none";
    });
  }

  closeBtns.forEach(btn => btn.addEventListener("click", e => {
    e.preventDefault();
    backdrop.style.display = "none";
  }));
});
</script>
</body>
</html>
