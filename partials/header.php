<?php
require __DIR__ . '/../config/bootstrap.php';
$TITLE = $TITLE ?? "Moja Digitalna Galerija";
?>
<!doctype html>
<html lang="bs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($TITLE) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css?v=1">
</head>
<body>
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="<?= htmlspecialchars($BASE_URL) ?>/">MojaGalerija<span>.ba</span></a>
      <nav>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/galerija">Galerija</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/umjetnici">Umjetnici</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/authenticity-certificate">Certifikat</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/faq">FAQ</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/o-nama">O nama</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/kontakt">Kontakt</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/admin">Administracija</a>
      <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<?php if (!empty($_SESSION["artist_id"])): ?>
<a class="nav-cta" href="<?= htmlspecialchars($BASE_URL) ?>/artist/dashboard">Moj profil</a>
<?php else: ?>
<a class="nav-cta" href="<?= htmlspecialchars($BASE_URL) ?>/login">Prijava</a>
<?php endif; ?>
</nav>
    </div>
  </header>
