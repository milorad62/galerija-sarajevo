<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../db.php';

if (!isset($_SESSION['artist_id'])) { header("Location: {$BASE_URL}/login"); exit; }
$TITLE = "Moji radovi";

$artistId = (int)$_SESSION['artist_id'];

// status column?
$hasStatus = false;
$colRes = $conn->query("SHOW COLUMNS FROM umjetnine LIKE 'status'");
if ($colRes && $colRes->num_rows > 0) { $hasStatus = true; }

$sql = "SELECT id, naslov, cijena, slika" . ($hasStatus ? ", status" : "") . " FROM umjetnine WHERE umjetnik_id = ? ORDER BY id DESC";
$st = $conn->prepare($sql);
$st->bind_param('i', $artistId);
$st->execute();
$rows = $st->get_result()->fetch_all(MYSQLI_ASSOC);
$st->close();
?>

<div class="card">
  <div class="row" style="justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
    <h1 style="margin:0;">Moji radovi</h1>
    <div class="row" style="gap:10px; flex-wrap:wrap;">
      <a class="btn primary" href="<?= htmlspecialchars($BASE_URL) ?>/artist/upload">➕ Upload</a>
      <a class="btn secondary" href="<?= htmlspecialchars($BASE_URL) ?>/artist/dashboard">Dashboard</a>
    </div>
  </div>

  <?php if (empty($rows)): ?>
    <p class="muted">Još nemate uploadovanih radova.</p>
  <?php else: ?>
    <div class="grid cols-3" style="margin-top:14px;">
      <?php foreach ($rows as $r): ?>
        <div class="card">
          <a href="<?= htmlspecialchars($BASE_URL) ?>/djelo?id=<?= (int)$r['id'] ?>" style="text-decoration:none;color:inherit;">
            <div class="title"><?= htmlspecialchars($r['naslov'] ?: 'Bez naslova') ?></div>
            <img src="<?= htmlspecialchars($BASE_URL) ?>/uploads/<?= htmlspecialchars($r['slika'] ?: 'placeholder.jpg') ?>" style="max-width:100%;height:auto;border-radius:10px;" alt="Umjetnina">
            <?php if (!empty($r['cijena'])): ?><div class="muted"><?= number_format((float)$r['cijena'], 2, ',', '.') ?> KM</div><?php endif; ?>
            <?php if (isset($r['status'])): ?><div class="chip" style="margin-top:8px;display:inline-block;"><?= htmlspecialchars($r['status']) ?></div><?php endif; ?>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
