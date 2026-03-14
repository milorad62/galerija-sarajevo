<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/security.php';
require __DIR__ . '/../db.php';

// Admin guard: prilagodi prema svom sistemu uloga
$isAdmin = false;
if (!empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) $isAdmin = true;
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') $isAdmin = true;
if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') $isAdmin = true;

if (!$isAdmin) {
  http_response_code(403);
  echo "<div style='padding:60px;text-align:center;'>";
  echo "<h2>Pristup odbijen</h2>";
  echo "<p>Ovaj dio je dostupan samo administratoru.</p>";
  echo "<p><a class='btn primary' href='" . htmlspecialchars($BASE_URL) . "/login?redirect=" . rawurlencode($BASE_URL . "/admin/") . "'>Prijava</a></p>";
  echo "</div>";
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
  <a href="<?= htmlspecialchars($BASE_URL) ?>/logout" class="btn danger">Odjava</a>
</div>

<div class="container">
  <div class="card" style="max-width:500px;margin:2rem auto;text-align:center;">
    <h2>Administracija</h2>
    <p>Odaberite tabelu koju želite pregledati:</p>
    <div style="display:flex;justify-content:center;gap:1rem;">
      <a href="umjetnici.php" class="btn primary">Umjetnici</a>
      <a href="umjetnine.php" class="btn">Umjetnine</a>
      <a href="moderacija.php" class="btn primary">Moderacija</a>
    </div>
  </div>
</div>
</body>
</html>
