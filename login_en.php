<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = trim($_POST['password'] ?? '');
  if ($email === '' || $pass === '') {
    $error = 'Please enter email and password.';
  } else {
    $stmt = $conn->prepare("SELECT * FROM umjetnici WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($u = $res->fetch_assoc()) {
      if (password_verify($pass, $u['lozinka'])) {
        $_SESSION['umjetnik_id'] = (int)$u['id'];
        $_SESSION['umjetnik_ime'] = trim(($u['ime'] ?? '').' '.($u['prezime'] ?? ''));
        header('Location: /galerija_sarajevo/pages/dodaj_umjetninu.php');
        exit;
      } else {
        $error = 'Invalid password.';
      }
    } else {
      $error = 'Artist not found.';
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Artist Login — MyGallery</title>
<link rel="stylesheet" href="/galerija_sarajevo/assets/css/styles.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<div class="header">
  <strong>MyGallery.ba</strong>
  <a href="/galerija_sarajevo/login.php" title="Bosnian">🇧🇦</a>
</div>

<div class="container">
  <div class="card" style="max-width:460px;margin:2rem auto;">
    <h2>Artist Login</h2>
    <?php if ($error): ?><div style="color:#b00;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <label class="label">Email</label>
      <input class="input" type="email" name="email" required>

      <label class="label">Password</label>
      <input class="input" type="password" name="password" required>

      <div style="margin-top:.8rem; display:flex; gap:.5rem;">
        <button class="btn primary">Sign In</button>
        <a class="btn" href="/galerija_sarajevo/">Back</a>
      </div>

      <div style="text-align:center; margin-top:1rem;">
        <span class="note">Not registered?</span>
        <a href="/galerija_sarajevo/register_en.php" style="color:#0a65c0; text-decoration:none; font-weight:600;">Sign up here</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
