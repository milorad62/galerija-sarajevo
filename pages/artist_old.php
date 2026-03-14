<link rel="stylesheet" href="/assets/css/styles.css">
<?php
$TITLE="Umjetnik — DigitalnaGalerija";
require __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<h1>Umjetnik nije pronađen</h1>";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM umjetnici WHERE id = ?");
$stmt->execute([$id]);
$artist = $stmt->fetch();

if (!$artist) {
    echo "<h1>Umjetnik nije pronađen</h1>";
    return;
}
?>
<div class="card">
  <h1><?= htmlspecialchars($artist['ime'] . ' ' . $artist['prezime']) ?></h1>
  <p><strong>Email:</strong> <?= htmlspecialchars($artist['email']) ?></p>
  <p><strong>Biografija:</strong><br><?= nl2br(htmlspecialchars($artist['biografija'])) ?></p>
</div>

<?php
// Get artworks of the artist
$stmt = $pdo->prepare("SELECT * FROM umjetnine WHERE umjetnik_id = ? ORDER BY id DESC");
$stmt->execute([$id]);
$artworks = $stmt->fetchAll();
?>

<h2>Djela umjetnika</h2>
<div class="grid cols-3">
<?php foreach ($artworks as $art): ?>
  <div class="card">
    <div class="title"><?= htmlspecialchars($art['naslov']) ?></div>
    <img src="/uploads/<?= htmlspecialchars($art['slika'] ?: 'placeholder.jpg') ?>" style="max-width:100%;height:auto;">
    <p><?= htmlspecialchars($art['opis']) ?></p>
    <div class="muted"><?= number_format($art['cijena'], 2) ?> EUR</div>
  </div>
<?php endforeach; ?>
</div>
