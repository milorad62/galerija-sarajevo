<?php
require __DIR__ . '/../config/bootstrap.php';
if (!isset($_SESSION['artist_id'])) { header("Location: {$BASE_URL}/login"); exit; }
$TITLE = "Moj profil — Dashboard";
?>
<div class="card">
  <h1>Dobrodošli, <?= htmlspecialchars($_SESSION['artist_name'] ?? 'Umjetnik') ?>!</h1>
  <div class="row" style="gap:10px; flex-wrap:wrap;">
    <a class="btn primary" href="<?= htmlspecialchars($BASE_URL) ?>/artist/upload">➕ Upload novog rada</a>
    <a class="btn secondary" href="<?= htmlspecialchars($BASE_URL) ?>/artist/my-artworks">🎨 Moji radovi</a>
    <a class="btn" href="<?= htmlspecialchars($BASE_URL) ?>/logout">🚪 Odjava</a>
  </div>
</div>
