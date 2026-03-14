<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';
$err='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $ime=trim($_POST['ime']??''); $prezime=trim($_POST['prezime']??''); $email=trim($_POST['email']??''); $pass=trim($_POST['password']??''); $conf=trim($_POST['confirm']??'');
  if(!$ime||!$prezime||!$email||!$pass||!$conf) $err='Sva polja su obavezna.';
  elseif($pass!==$conf) $err='Lozinke se ne poklapaju.';
  else{
    $chk=$conn->prepare("SELECT id FROM umjetnici WHERE email=?"); $chk->bind_param('s',$email); $chk->execute(); $r=$chk->get_result();
    if($r->fetch_assoc()) $err='E-mail je već registrovan.';
    else { $hash=password_hash($pass, PASSWORD_DEFAULT);
      $ins=$conn->prepare("INSERT INTO umjetnici (ime, prezime, email, lozinka, biografija) VALUES (?,?,?,?, '')");
      $ins->bind_param('ssss',$ime,$prezime,$email,$hash);
      if($ins->execute()){ header('Location: /galerija_sarajevo/login.php'); exit; } else $err='Greška pri registraciji.';
    }
  }
}
?><!doctype html><html lang="bs"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registracija — MojaGalerija</title>
<link rel="stylesheet" href="/galerija_sarajevo/assets/css/styles.css">
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head><body>
<div class="header"><strong>MojaGalerija.ba</strong><div><a href="/galerija_sarajevo/login.php">Prijava</a></div></div>
<div class="container"><div class="card" style="max-width:520px;margin:2rem auto;">
<h2>Registracija umjetnika</h2>
<?php if($err): ?><div style="color:#b00;"><?= htmlspecialchars($err) ?></div><?php endif; ?>
<form method="post">
<label class="label">Ime</label><input class="input" type="text" name="ime" required>
<label class="label">Prezime</label><input class="input" type="text" name="prezime" required>
<label class="label">E-mail</label><input class="input" type="email" name="email" required>
<label class="label">Lozinka</label><input class="input" type="password" name="password" required>
<label class="label">Potvrda lozinke</label><input class="input" type="password" name="confirm" required>
<div style="margin-top:.8rem;display:flex;gap:.5rem;"><button class="btn primary">Kreiraj nalog</button><a class="btn" href="/galerija_sarajevo/">Nazad</a></div>
</form></div></div></body></html>
