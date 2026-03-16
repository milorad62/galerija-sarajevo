<?php
$TITLE = "Umjetnici — Digitalna Galerija";
require __DIR__ . '/../db.php';

$artists = $pdo->query("SELECT id, ime, prezime, email FROM umjetnici ORDER BY prezime, ime")->fetchAll();
?>

<h1>Umjetnici</h1>

<div class="grid cols-3">
<?php foreach ($artists as $a): ?>
  <div class="card">
    <h3>
      <a href="/artist?id=<?= $a['id'] ?>">
        <?= htmlspecialchars($a['ime'] . ' ' . $a['prezime']) ?>
      </a>
    </h3>
    <p class="muted"><?= htmlspecialchars($a['email'] ?: '') ?></p>
    <a class="badge" href="/galerija_sarajevo/artist?id=<?= $a['id'] ?>">Pogledaj djela</a>
  </div>
<?php endforeach; ?>
</div>
