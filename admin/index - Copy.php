<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }
?><!doctype html><html lang="bs"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — MojaGalerija</title><link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
</head><body>
<div class="header"><strong>MojaGalerija.ba — Admin</strong><nav>
  <a href="/galerija_sarajevo/admin/umjetnici.php">Umjetnici</a>
  <a href="/galerija_sarajevo/admin/umjetnine.php">Umjetnine</a>
  <a href="/galerija_sarajevo/logout.php">Odjava</a>
</nav></div>
<div class="container"><div class="card"><h2>Kontrolna tabla</h2><p>Odaberite sekciju u meniju.</p></div></div></body></html>
