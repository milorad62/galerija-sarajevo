<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }
$list=$conn->query("SELECT id, ime, prezime, email, LEFT(biografija,120) AS bio FROM umjetnici ORDER BY id DESC");
?><!doctype html><html lang="bs"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Umjetnici — Admin</title><link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css"></head><body>
<div class="header"><strong>MojaGalerija.ba — Admin</strong><nav>
  <a href="/galerija_sarajevo/admin/">Početna</a>
  <a href="/galerija_sarajevo/admin/umjetnine.php">Umjetnine</a>
</nav></div>
<div class="container"><div class="card"><h2>Umjetnici</h2>
<table class="table"><thead><tr><th>ID</th><th>Ime i prezime</th><th>E-mail</th><th>Bio</th></tr></thead><tbody>
<?php while($r=$list->fetch_assoc()): ?><tr>
<td><?= (int)$r['id'] ?></td><td><?= htmlspecialchars(($r['ime']??'').' '.($r['prezime']??'')) ?></td>
<td><?= htmlspecialchars($r['email']??'') ?></td><td><?= htmlspecialchars($r['bio']??'') ?></td></tr>
<?php endwhile; ?></tbody></table></div></div></body></html>
