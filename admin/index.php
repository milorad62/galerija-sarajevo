<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/security.php';
require __DIR__ . '/../db.php';

$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

// Admin guard
$isAdmin = false;

// podrži više mogućih session varijabli
if (!empty($_SESSION['admin_id'])) {
    $isAdmin = true;
}
if (!empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) {
    $isAdmin = true;
}
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $isAdmin = true;
}
if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $isAdmin = true;
}

if (!$isAdmin) {
    header('Location: ' . $BASE_URL . '/login.php?redirect=' . urlencode($BASE_URL . '/admin/'));
    exit;
}
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin panel — MojaGalerija</title>
<link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
</head>
<body>
<div class="header">
    <strong>Admin panel — MojaGalerija</strong>
   <a href="<?= htmlspecialchars($BASE_URL) ?>/logout.php" class="btn danger">Odjava</a>
</div>

<div class="container">
    <div class="card" style="max-width:500px;margin:2rem auto;text-align:center;">
        <h2>Administracija</h2>
        <p>Odaberite tabelu koju želite pregledati:</p>
        <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;">
            <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/umjetnici.php" class="btn primary">Umjetnici</a>
            <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/umjetnine.php" class="btn">Umjetnine</a>
            <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/moderacija.php" class="btn primary">Moderacija</a>
        </div>
    </div>
</div>
</body>
</html>
