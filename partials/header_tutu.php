<?php
$TITLE = $TITLE ?? "Moja Digitalna Galerija";
?>
<!doctype html>
<html lang="bs">
<head>
    <link rel="stylesheet" href="/assets/css/styles.css">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($TITLE) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/styles.css?v=1">
</head>
<body>
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="/">MojaGalerija<span>.ba</span></a>
      <nav>
        <a href="./galerija">Galerija</a>
        <a href="./umjetnici">Umjetnici</a>
        <a href="./authenticity-certificate">Certifikat</a>
        <a href="./faq">FAQ</a>
        <a href="./o-nama">O nama</a>
        <a class="btn" href="./kontakt">Kontakt</a>
        <a href="./OnlineArtSalesPlatform">Registracija</a>
      </nav>
    </div>
  </header>
  <main class="container">
