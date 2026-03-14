<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['ime'] ?? '');
  $last = trim($_POST['prezime'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = trim($_POST['password'] ?? '');
  $conf = trim($_POST['confirm'] ?? '');

  if (!$first || !$last || !$email || !$pass || !$conf) {
    $err = 'All fields are required.';
  } elseif ($pass !== $conf) {
    $err = 'Passwords do not match.';
  } else {
    $chk = $conn->prepare("SELECT id FROM umjetnici WHERE email=?");
    $chk->bind_param('s', $email);
    $chk->execute();
    $r = $chk->get_result();
    if ($r->fetch_assoc()) {
      $err = 'Email already registered.';
    } else {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $ins = $conn->prepare("INSERT INTO umjetnici (ime, prezime, email, lozinka, biografija) VALUES (?,?,?,?, '')");
      $ins->bind_param('ssss', $first, $last, $email, $hash);
      if ($ins->execute()) {
        header('Location: /galerija_sarajevo/login_en.php');
        exit;
      } else {
        $err = 'Registration failed.';
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Artist Registration — MyGallery</title>
<link rel="stylesheet" href="/galerija_sarajevo/assets/css/styles.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<div class="header">
  <strong>MyGallery.ba</strong>
  <a href="/galerija_sarajevo/register.php" title="Bosnian">🇧🇦</a>
</div>

<div class="container">
  <div class="card" style="max-width:520px;margin:2rem auto;">
    <h2>Artist Registration</h2>
    <?php if ($err): ?><div style="color:#b00;"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post">
      <label class="label">First Name</label>
      <input class="input" type="text" name="ime" required>

      <label class="label">Last Name</label>
      <input class="input" type="text" name="prezime" required>

      <label class="label">Email</label>
      <input class="input" type="email" name="email" required>

      <label class="label">Password</label>
      <input class="input" type="password" name="password" required>

      <label class="label">Confirm Password</label>
      <input class="input" type="password" name="confirm" required>

      <div style="margin-top:.8rem; display:flex; gap:.5rem;">
        <button class="btn primary">Create Account</button>
        <a class="btn" href="/galerija_sarajevo/login_en.php">Back</a>
      </div>

      <div style="text-align:center; margin-top:1rem;">
        <span class="note">Already have an account?</span>
        <a href="/galerija_sarajevo/login_en.php" style="color:#0a65c0; text-decoration:none; font-weight:600;">Sign in here</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
