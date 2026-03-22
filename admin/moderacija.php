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
    header('Location: ' . $BASE_URL . '/login.php?redirect=' . urlencode($BASE_URL . '/admin/moderacija.php'));
    exit;
}

$msg = '';

// Provjeri da li postoje kolone status/featured
$cols = [];
$q = $conn->query("SHOW COLUMNS FROM umjetnine");
if ($q) {
    while ($r = $q->fetch_assoc()) {
        $cols[] = $r['Field'];
    }
}
$hasStatus   = in_array('status', $cols, true);
$hasFeatured = in_array('featured', $cols, true);

if (!$hasStatus) {
    ?>
    <!doctype html>
    <html lang="bs">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Moderacija — Admin panel</title>
        <link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
    </head>
    <body>
    <div class="header">
        <strong>Moderacija</strong>
        <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/" class="btn">Nazad</a>
    </div>

    <div class="container" style="padding:30px;">
        <div class="card">
            <h2>Nedostaje kolona <code>status</code> u tabeli umjetnine</h2>
            <p>Pokreni u bazi ovaj SQL:</p>
            <pre style="background:#f5f5f5;padding:14px;border-radius:10px;overflow:auto;">ALTER TABLE umjetnine
ADD status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending';</pre>

            <?php if (!$hasFeatured): ?>
                <p>A za featured i ovo:</p>
                <pre style="background:#f5f5f5;padding:14px;border-radius:10px;overflow:auto;">ALTER TABLE umjetnine
ADD featured TINYINT(1) NOT NULL DEFAULT 0;</pre>
            <?php endif; ?>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Akcije
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $msg = "CSRF greška. Osvježi stranicu.";
    } elseif (!rate_limit('admin_mod', 1)) {
        $msg = "Prebrzo. Pokušaj ponovo.";
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($id > 0) {
            if ($action === 'approve') {
                $st = $conn->prepare("UPDATE umjetnine SET status = 'approved' WHERE id = ?");
                if (!$st) die("SQL prepare error: " . $conn->error);
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();

                header('Location: ' . $BASE_URL . '/admin/moderacija.php');
                exit;
            }

            if ($action === 'reject') {
                $st = $conn->prepare("UPDATE umjetnine SET status = 'rejected' WHERE id = ?");
                if (!$st) die("SQL prepare error: " . $conn->error);
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();

                header('Location: ' . $BASE_URL . '/admin/moderacija.php');
                exit;
            }

            if ($action === 'toggle_featured' && $hasFeatured) {
                $featured = (int)($_POST['featured'] ?? 0);

                $st = $conn->prepare("UPDATE umjetnine SET featured = ? WHERE id = ?");
                if (!$st) die("SQL prepare error: " . $conn->error);
                $st->bind_param('ii', $featured, $id);
                $st->execute();
                $st->close();

                header('Location: ' . $BASE_URL . '/admin/moderacija.php');
                exit;
            }

            if ($action === 'delete') {
                $st = $conn->prepare("SELECT slika FROM umjetnine WHERE id = ?");
                if (!$st) die("SQL prepare error: " . $conn->error);
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
                if (!$st) die("SQL prepare error: " . $conn->error);
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();

                header('Location: ' . $BASE_URL . '/admin/moderacija.php');
                exit;
            }
        }
    }
}

// Pending list
$sql = "SELECT 
            u.id, u.naslov, u.opis, u.cijena, u.slika, u.status"
            . ($hasFeatured ? ", u.featured" : "") . ",
            a.ime, a.prezime
        FROM umjetnine u
        LEFT JOIN umjetnici a ON u.umjetnik_id = a.id
        WHERE u.status = 'pending'
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
<title>Moderacija — Admin panel</title>
<link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
</head>
<body>
<div class="header">
    <strong>Moderacija — Pending umjetnine</strong>
    <a href="<?= htmlspecialchars($BASE_URL) ?>/admin/" class="btn">Nazad</a>
</div>

<div class="container">
    <div class="card" style="margin-bottom:16px;">
        <p class="muted">Ovdje odobravaš ili odbijaš nove uploadove. Po želji označi <b>Featured</b> za prikaz na naslovnoj.</p>
    </div>

    <?php if (!empty($msg)): ?>
        <p style="color:#c00"><b><?= htmlspecialchars($msg) ?></b></p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;">
        <tr style="background:#0a65c0;color:#fff;">
            <th>ID</th>
            <th>Naslov</th>
            <th>Autor</th>
            <th>Cijena</th>
            <th>Slika</th>
            <th>Status</th>
            <?php if ($hasFeatured): ?><th>Featured</th><?php endif; ?>
            <th>Akcije</th>
        </tr>

        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($r = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['naslov'] ?? '') ?></td>
                    <td><?= htmlspecialchars(trim(($r['ime'] ?? '') . ' ' . ($r['prezime'] ?? ''))) ?></td>
                    <td><?= !empty($r['cijena']) ? number_format((float)$r['cijena'], 2, ',', '.') . ' KM' : '-' ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($BASE_URL) ?>/uploads/<?= htmlspecialchars($r['slika'] ?? 'placeholder.jpg') ?>" style="width:90px;border-radius:8px;" alt="Umjetnina">
                    </td>
                    <td><span class="chip"><?= htmlspecialchars($r['status'] ?? '') ?></span></td>

                    <?php if ($hasFeatured): ?>
                        <td>
                            <form method="post" action="<?= htmlspecialchars($BASE_URL) ?>/admin/moderacija.php" style="margin:0;">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="action" value="toggle_featured">
                                <input type="hidden" name="featured" value="<?= !empty($r['featured']) ? 0 : 1 ?>">
                                <button class="btn <?= !empty($r['featured']) ? 'primary' : '' ?>" type="submit">
                                    <?= !empty($r['featured']) ? 'DA' : 'NE' ?>
                                </button>
                            </form>
                        </td>
                    <?php endif; ?>

                    <td>
                        <div class="row" style="gap:8px;flex-wrap:wrap;">
                            <form method="post" action="<?= htmlspecialchars($BASE_URL) ?>/admin/moderacija.php" style="margin:0;">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button class="btn primary" type="submit">Odobri</button>
                            </form>

                            <form method="post" action="<?= htmlspecialchars($BASE_URL) ?>/admin/moderacija.php" style="margin:0;">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button class="btn" type="submit">Odbij</button>
                            </form>

                            <form method="post" action="<?= htmlspecialchars($BASE_URL) ?>/admin/moderacija.php" style="margin:0;" onsubmit="return confirm('Obrisati ovu umjetninu?');">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button class="btn danger" type="submit">Obriši</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?= $hasFeatured ? '8' : '7' ?>" style="text-align:center;">
                    Trenutno nema radova koji čekaju moderaciju.
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
