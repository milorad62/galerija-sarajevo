<?php
require __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/db.php';

$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

$error = '';

// Prihvati redirect iz URL-a, ali samo interne putanje
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? ($BASE_URL . '/pages/dodaj_umjetninu.php');

if (!is_string($redirect) || $redirect === '') {
    $redirect = $BASE_URL . '/pages/dodaj_umjetninu.php';
}

// sigurnost: dozvoli samo interne putanje koje počinju sa /
if (strpos($redirect, '/') !== 0) {
    $redirect = $BASE_URL . '/pages/dodaj_umjetninu.php';
}

// zabrani full external URL
if (preg_match('~^https?://~i', $redirect)) {
    $redirect = $BASE_URL . '/pages/dodaj_umjetninu.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if ($email === '' || $pass === '') {
        $error = 'Unesite e-mail i lozinku.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM umjetnici WHERE email = ? LIMIT 1");

        if (!$stmt) {
            die("SQL prepare error: " . $conn->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();

        $res = $stmt->get_result();
        $u = $res ? $res->fetch_assoc() : null;

        $stmt->close();

        if ($u) {
            if (password_verify($pass, $u['lozinka'])) {
                // standardizovane session varijable
                $_SESSION['artist_id'] = (int)$u['id'];
                $_SESSION['artist_name'] = trim(($u['ime'] ?? '') . ' ' . ($u['prezime'] ?? ''));

                // kompatibilnost sa starijim dijelovima projekta
                $_SESSION['umjetnik_id'] = (int)$u['id'];
                $_SESSION['umjetnik_ime'] = trim(($u['ime'] ?? '') . ' ' . ($u['prezime'] ?? ''));

                // ako admin dio koristi ove session ključeve, postavi i njih
                $_SESSION['admin_id'] = (int)$u['id'];
                $_SESSION['admin_name'] = trim(($u['ime'] ?? '') . ' ' . ($u['prezime'] ?? ''));

                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Pogrešna lozinka.';
            }
        } else {
            $error = 'Korisnik ne postoji.';
        }
    }
}
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prijava — MojaGalerija</title>
<link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<div class="header">
    <strong>MojaGalerija.ba</strong>
    <a href="<?= htmlspecialchars($BASE_URL) ?>/login_en.php" title="English">🇬🇧</a>
</div>

<div class="container">
    <div class="card" style="max-width:460px;margin:2rem auto;">
        <h2>Prijava umjetnika</h2>

        <?php if ($error): ?>
            <div style="color:#b00; margin-bottom:12px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

            <label class="label">E-mail</label>
            <input class="input" type="email" name="email" required>

            <label class="label">Lozinka</label>
            <input class="input" type="password" name="password" required>

            <div style="margin-top:.8rem; display:flex; gap:.5rem; flex-wrap:wrap;">
                <button class="btn primary" type="submit">Prijavi se</button>
                <a class="btn" href="<?= htmlspecialchars($BASE_URL) ?>/">Nazad</a>
            </div>

            <div style="text-align:center; margin-top:1rem;">
                <span class="note">Niste registrovani?</span>
                <a href="<?= htmlspecialchars($BASE_URL) ?>/register.php" style="color:#0a65c0; text-decoration:none; font-weight:600;">
                    Registrujte se ovdje
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
