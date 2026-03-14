<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/security.php';
require __DIR__ . '/../db.php';

if (!isset($_SESSION['artist_id'])) { header("Location: {$BASE_URL}/login"); exit; }
$TITLE = "Upload umjetnine";

$err = '';
$ok  = '';

// Provjeri kolone u tabeli umjetnine
$cols = [];
$q = $conn->query("SHOW COLUMNS FROM umjetnine");
if ($q) { while($r = $q->fetch_assoc()) { $cols[] = $r['Field']; } }
$hasFeatured = in_array('featured', $cols);
$hasStatus   = in_array('status', $cols);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['_csrf'] ?? null)) {
    $err = "Sigurnosna provjera nije prošla. Osvježi stranicu i pokušaj ponovo.";
  } elseif (!rate_limit('artist_upload', 10)) {
    $err = "Molimo sačekajte nekoliko sekundi prije novog pokušaja.";
  } else {
    $naslov = clean_text($_POST['naslov'] ?? '', 120);
    $opis   = clean_text($_POST['opis'] ?? '', 2000);
    $cijena = isset($_POST['cijena']) ? (float)$_POST['cijena'] : 0.0;
    $featured = !empty($_POST['featured']) ? 1 : 0;

    if ($naslov === '') $err = "Unesite naslov.";
    if (!$err && (empty($_FILES['slika']) || $_FILES['slika']['error'] !== UPLOAD_ERR_OK)) $err = "Odaberite sliku.";
    if (!$err) {
      $max = 5 * 1024 * 1024;
      if ($_FILES['slika']['size'] > $max) $err = "Slika je prevelika (max 5MB).";

      $allowed = ['jpg','jpeg','png','webp'];
      $ext = strtolower(pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, $allowed)) $err = "Dozvoljeni formati: JPG, PNG, WEBP.";

      // Snimi sliku
      if (!$err) {
        $dir = __DIR__ . '/../uploads';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $newName = bin2hex(random_bytes(10)) . '.' . $ext;
        $dst = $dir . '/' . $newName;
        if (!move_uploaded_file($_FILES['slika']['tmp_name'], $dst)) {
          $err = "Neuspješan upload slike.";
        } else {
          // Insert u bazu
          $artistId = (int)$_SESSION['artist_id'];
          $status = $hasStatus ? 'pending' : null;

          if ($hasFeatured && $hasStatus) {
            $st = $conn->prepare("INSERT INTO umjetnine (umjetnik_id, naslov, opis, cijena, slika, featured, status) VALUES (?,?,?,?,?,?,?)");
            $st->bind_param('issdsis', $artistId, $naslov, $opis, $cijena, $newName, $featured, $status);
          } elseif ($hasFeatured) {
            $st = $conn->prepare("INSERT INTO umjetnine (umjetnik_id, naslov, opis, cijena, slika, featured) VALUES (?,?,?,?,?,?)");
            $st->bind_param('issdsi', $artistId, $naslov, $opis, $cijena, $newName, $featured);
          } elseif ($hasStatus) {
            $st = $conn->prepare("INSERT INTO umjetnine (umjetnik_id, naslov, opis, cijena, slika, status) VALUES (?,?,?,?,?,?)");
            $st->bind_param('issdss', $artistId, $naslov, $opis, $cijena, $newName, $status);
          } else {
            $st = $conn->prepare("INSERT INTO umjetnine (umjetnik_id, naslov, opis, cijena, slika) VALUES (?,?,?,?,?)");
            $st->bind_param('issds', $artistId, $naslov, $opis, $cijena, $newName);
          }

          if ($st && $st->execute()) {
            $ok = "Umjetnina je uploadovana. " . ($hasStatus ? "Čeka odobrenje admina." : "");
          } else {
            $err = "Greška pri upisu u bazu.";
          }
          if ($st) $st->close();
        }
      }
    }
  }
}
?>

<div class="card">
  <h1>Upload umjetnine</h1>
  <?php if ($err): ?><p style="color:#c00"><b><?= htmlspecialchars($err) ?></b></p><?php endif; ?>
  <?php if ($ok): ?><p style="color:#0a7"><b><?= htmlspecialchars($ok) ?></b></p><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
    <label class="muted">Naslov</label>
    <input class="input" type="text" name="naslov" required>

    <label class="muted" style="display:block;margin-top:10px;">Opis</label>
    <textarea class="input" name="opis" rows="5" placeholder="Kratak opis..."></textarea>

    <div class="row" style="gap:12px; margin-top:10px; flex-wrap:wrap;">
      <div style="min-width:220px;flex:1">
        <label class="muted">Cijena (KM)</label>
        <input class="input" type="number" name="cijena" step="0.01" min="0">
      </div>
      <div style="min-width:220px;flex:1">
        <label class="muted">Slika</label>
        <input class="input" type="file" name="slika" accept=".jpg,.jpeg,.png,.webp" required>
      </div>
    </div>

    <?php if ($hasFeatured): ?>
    <label style="display:flex;gap:8px;align-items:center;margin-top:12px;">
      <input type="checkbox" name="featured" value="1"> Označi kao featured
    </label>
    <?php endif; ?>

    <div class="row" style="gap:10px; margin-top:14px;">
      <button class="btn primary" type="submit">Upload</button>
      <a class="btn secondary" href="<?= htmlspecialchars($BASE_URL) ?>/artist/dashboard">Nazad</a>
    </div>
  </form>
</div>
