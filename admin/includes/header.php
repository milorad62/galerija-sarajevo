<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'bs');
$_SESSION['lang'] = in_array($lang, ['bs','en']) ? $lang : 'bs';
require_once __DIR__ . '/../lang/' . $_SESSION['lang'] . '.php';
$TITLE = $TITLE ?? $T['admin_panel'];
?>
<!doctype html>
<html lang="<?= htmlspecialchars($_SESSION['lang']) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($TITLE) ?> — Galerija Sarajevo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <style>
    body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
    .container { max-width: 1100px; margin: 0 auto; padding: 1rem; }
    .site-header { background: #0a65c0; color: #fff; }
    .site-header .nav { display:flex; align-items:center; justify-content:space-between; padding: .75rem 1rem; }
    .brand { font-weight: 800; font-size: 1.25rem; color: #fff; text-decoration:none; }
    .brand span{opacity:.85}
    .nav a{ color:#fff; margin-left:1rem; text-decoration:none; font-weight:600; }
    .breadcrumbs{ background:#f4f7fb; padding:.5rem 1rem; border-bottom:1px solid #e6edf7; }
    .card{ background:#fff; border:1px solid #e7eef7; border-radius:14px; padding:1rem; box-shadow:0 1px 2px rgba(0,0,0,.04); }
    .grid{ display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(260px,1fr)); }
    table{ width:100%; border-collapse: collapse; }
    th, td{ border-bottom:1px solid #eef2f7; padding:.6rem .5rem; text-align:left; font-size:.95rem; }
    th{ background:#f8fbff; }
    .btn{ display:inline-block; padding:.5rem .8rem; border-radius:10px; text-decoration:none; border:1px solid #d8e4f5; }
    .btn-primary{ background:#0a65c0; color:#fff; border-color:#0a65c0; }
    .btn-danger{ background:#d33; color:#fff; border-color:#d33; }
    .btn-light{ background:#fff; color:#0a65c0; }
    .muted{ color:#6b7b90; }
    .toolbar{ display:flex; flex-wrap:wrap; gap:.5rem; align-items:center; margin:.5rem 0 1rem; }
    input[type="text"], select{ border:1px solid #d8e4f5; border-radius:10px; padding:.5rem .6rem; }
    .lang-toggle a{ color:#fff; opacity:.9; margin-left:.5rem; }
  </style>
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a class="brand" href="../"><?= $T['brand'] ?><span>.ba</span></a>
    <nav>
      <a href="../"><?= $T['menu_home'] ?></a>
      <a href="../galerija"><?= $T['menu_gallery'] ?></a>
      <a href="./"><?= $T['admin_panel'] ?></a>
      <span class="lang-toggle">🌐
        <a href="?lang=bs">BS</a> | <a href="?lang=en">EN</a>
      </span>
    </nav>
  </div>
</header>
<div class="breadcrumbs">
  <div class="container">
    <a href="../" class="muted"><?= $T['brand'] ?></a> › <strong><?= $T['admin_panel'] ?></strong>
  </div>
</div>
<main class="container">
