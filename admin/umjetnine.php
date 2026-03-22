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
if (!empty($_SESSION['admin_id'])) $isAdmin = true;
if (!empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) $isAdmin = true;
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') $isAdmin = true;
if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') $isAdmin = true;

if (!$isAdmin) {
    header('Location: ' . $BASE_URL . '/login.php?redirect=' . urlencode($BASE_URL . '/admin/umjetnine.php'));
    exit;
}

$flash = '';

// Provjera kolona
$cols = [];
$q = $conn->query("SHOW COLUMNS FROM umjetnine");
if ($q) {
    while ($r = $q->fetch_assoc()) {
        $cols[] = $r['Field'];
    }
}
$hasStatus = in_array('status', $cols, true);
$hasFeatured = in_array('featured', $cols, true);

// Delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $flash = "CSRF greška.";
    } else {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $st = $conn->prepare("SELECT slika FROM umjetnine WHERE id = ?");
            if (!$st) {
                die("SQL prepare error: " . $conn->error);
            }

            $st->bind_param('i', $id);
            $st->execute();
            $resImg = $st->get_result();
            $imgRow = $resImg ? $resImg->fetch_assoc() : null;
            $st->close();

            if (!empty($imgRow['slika'])) {
                $fp = __DIR__ . '/../uploads/' . basename($imgRow['slika']);
                if (file_exists($fp)) {
                    @unlink($fp);
                }
            }

            $st = $conn->prepare("DELETE FROM umjetnine WHERE id = ?");
            if (!$st) {
                die("SQL prepare error: " . $conn->error);
            }

            $st->bind_param('i', $id);
            $st->execute();
            $st->close();

            header('Location: ' . $BASE_URL . '/admin/umjetnine.php');
            exit;
        }
    }
}

$sql = "SELECT 
            u.id, 
            u.naslov, 
            u.opis, 
            u.cijena, 
            u.slika"
        . ($hasFeatured ? ", u.featured" : "")
        . ($hasStatus ? ", u.status" : "") .
        ", a.ime, a.prezime
        FROM umjetnine u
        LEFT JOIN umjetnici a ON u.umjetnik_id = a.id
        ORDER BY u.id DESC";

$res = $conn->query($sql);
if (!$res) {
    die("SQL query error: " . $conn->error);
}
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Umjetnine — Admin panel</title>
<link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
</head>
<body>
<div class="header">
    <strong>Umjetnine</strong>
    <div class="row" style="gap:10px;">
        <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/moderacija.php" class="btn primary">Moderacija</a>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/" class="btn">Nazad</a>
    </div>
</div>

<div class="container">
    <?php if (!empty($flash)): ?>
        <p style="color:#c00"><b><?= htmlspecialchars($flash) ?></b></p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;">
        <tr style="background:#0a65c0;color:#fff;">
            <th>ID</th>
            <th>Naslov</th>
            <th>Autor</th>
            <th>Cijena</th>
            <th>Slika</th>
            <?php if ($hasStatus): ?><th>Status</th><?php endif; ?>
            <?php if ($hasFeatured): ?><th>Featured</th><?php endif; ?>
            <th>Brisanje</th>
        </tr>

        <?php while ($r = $res->fetch_assoc()): ?>
            <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['naslov'] ?? '') ?></td>
                <td><?= htmlspecialchars(trim(($r['ime'] ?? '') . ' ' . ($r['prezime'] ?? ''))) ?></td>
                <td>
                    <?= !empty($r['cijena']) ? number_format((float)$r['cijena'], 2, ',', '.') . ' KM' : '-' ?>
                </td>
                <td>
                    <img
                        src="<?= htmlspecialchars($BASE_URL) ?>/uploads/<?= htmlspecialchars($r['slika'] ?? 'placeholder.jpg') ?>"
                        style="width:80px;border-radius:6px;"
                        alt="Umjetnina"
                    >
                </td>
                <?php if ($hasStatus): ?>
                    <td><span class="chip"><?= htmlspecialchars($r['status'] ?? '') ?></span></td>
                <?php endif; ?>
                <?php if ($hasFeatured): ?>
                    <td><?= !empty($r['featured']) ? 'DA' : 'NE' ?></td>
                <?php endif; ?>
                <td>
                    <form method="post" action="<?= htmlspecialchars($BASE_URL) ?>/admin/umjetnine.php" onsubmit="return confirm('Obrisati ovu umjetninu?');" style="margin:0;">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button class="btn danger" type="submit">Obriši</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
