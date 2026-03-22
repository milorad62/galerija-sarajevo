<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../db.php';

$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

// Admin guard
$isAdmin = false;
if (!empty($_SESSION['admin_id'])) $isAdmin = true;
if (!empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) $isAdmin = true;
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') $isAdmin = true;
if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') $isAdmin = true;

if (!$isAdmin) {
    header('Location: ' . $BASE_URL . '/login.php?redirect=' . urlencode($BASE_URL . '/admin/umjetnici.php'));
    exit;
}

// Brisanje ide prije HTML-a
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM umjetnici WHERE id = ?");
    if (!$stmt) {
        die("SQL prepare error: " . $conn->error);
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    header('Location: ' . $BASE_URL . '/admin/umjetnici.php');
    exit;
}

$res = $conn->query("SELECT id, ime, prezime, email, biografija FROM umjetnici ORDER BY id DESC");
if (!$res) {
    die("SQL query error: " . $conn->error);
}
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Umjetnici — Admin panel</title>
<link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
</head>
<body>
<div class="header">
    <strong>Umjetnici</strong>
    <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/" class="btn">Nazad</a>
</div>

<div class="container">
    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;">
        <tr style="background:#0a65c0;color:#fff;">
            <th>ID</th>
            <th>Ime</th>
            <th>Prezime</th>
            <th>Email</th>
            <th>Biografija</th>
            <th>Brisanje</th>
        </tr>

        <?php while ($r = $res->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['ime'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['prezime'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '') ?></td>
            <td><?= nl2br(htmlspecialchars($r['biografija'] ?? '')) ?></td>
            <td>
                <form method="post" action="<?= htmlspecialchars($BASE_URL) ?>/admin/umjetnici.php" onsubmit="return confirm('Obrisati ovog umjetnika?');">
                    <input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>">
                    <button type="submit" class="btn danger">Obriši</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
