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
<?php
// Provjeri da li postoje kolone status/featured
$cols = [];
$q = $conn->query("SHOW COLUMNS FROM umjetnine");
if ($q) { while($r=$q->fetch_assoc()) { $cols[] = $r['Field']; } }
$hasStatus = in_array('status', $cols);
$hasFeatured = in_array('featured', $cols);

if (!$hasStatus) {
  echo "<div class='container' style='padding:30px;'><div class='card'>";
  echo "<h2>Nedostaje kolona <code>status</code> u tabeli umjetnine</h2>";
  echo "<p>Pokreni migraciju: <code>docs/marketplace_migracije.sql</code>.</p>";
  echo "<p><a class='btn' href='index.php'>Nazad</a></p>";
  echo "</div></div>";
  exit;
}

// Akcije (approve/reject/feature/delete)
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
        $st = $conn->prepare("UPDATE umjetnine SET status='approved' WHERE id=?");
        $st->bind_param('i', $id);
        $st->execute();
        $st->close();
      } elseif ($action === 'reject') {
        $st = $conn->prepare("UPDATE umjetnine SET status='rejected' WHERE id=?");
        $st->bind_param('i', $id);
        $st->execute();
        $st->close();
      } elseif ($action === 'toggle_featured' && $hasFeatured) {
        $featured = (int)($_POST['featured'] ?? 0);
        $st = $conn->prepare("UPDATE umjetnine SET featured=? WHERE id=?");
        $st->bind_param('ii', $featured, $id);
        $st->execute();
        $st->close();
      } elseif ($action === 'delete') {
        // pobriši i sliku
        $st = $conn->prepare("SELECT slika FROM umjetnine WHERE id=?");
        $st->bind_param('i', $id);
        $st->execute();
        $res = $st->get_result()->fetch_assoc();
        $st->close();
        if (!empty($res['slika'])) {
          $fp = __DIR__ . '/../uploads/' . basename($res['slika']);
          if (file_exists($fp)) { @unlink($fp); }
        }
        $st = $conn->prepare("DELETE FROM umjetnine WHERE id=?");
        $st->bind_param('i', $id);
        $st->execute();
        $st->close();
      }
    }
  }
}

// Pending list
$sql = "SELECT u.id, u.naslov, u.opis, u.cijena, u.slika, u.featured, u.status, a.ime, a.prezime
        FROM umjetnine u
        LEFT JOIN umjetnici a ON u.umjetnik_id=a.id
        WHERE u.status='pending'
        ORDER BY u.id DESC";
$res = $conn->query($sql);
?>
<div class="header">
  <strong>Moderacija — Pending umjetnine</strong>
  <a href="index.php" class="btn">Nazad</a>
</div>

<div class="container">
  <div class="card" style="margin-bottom:16px;">
    <p class="muted">Ovdje odobravaš ili odbijaš nove uploadove. Po želji označi <b>Featured</b> (prikaz na naslovnoj).</p>
  </div>

  <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;">
    <tr style="background:#0a65c0;color:#fff;">
      <th>ID</th><th>Naslov</th><th>Autor</th><th>Cijena</th><th>Slika</th><th>Status</th><th>Featured</th><th>Akcije</th>
    </tr>
    <?php if($res): while($r=$res->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['naslov'] ?? '') ?></td>
        <td><?= htmlspecialchars(($r['ime'] ?? '') . ' ' . ($r['prezime'] ?? '')) ?></td>
        <td><?= !empty($r['cijena']) ? number_format((float)$r['cijena'],2,',','.') . ' KM' : '-' ?></td>
        <td>
          <img src="<?= htmlspecialchars($BASE_URL) ?>/uploads/<?= htmlspecialchars($r['slika'] ?? 'placeholder.jpg') ?>" style="width:90px;border-radius:8px;">
        </td>
        <td><span class="chip"><?= htmlspecialchars($r['status'] ?? '') ?></span></td>
        <td>
          <?php if($hasFeatured): ?>
            <form method="post" style="margin:0;">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="toggle_featured">
              <input type="hidden" name="featured" value="<?= !empty($r['featured']) ? 0 : 1 ?>">
              <button class="btn <?= !empty($r['featured']) ? 'primary' : '' ?>" type="submit">
                <?= !empty($r['featured']) ? 'DA' : 'NE' ?>
              </button>
            </form>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
        <td>
          <div class="row" style="gap:8px;flex-wrap:wrap;">
            <form method="post" style="margin:0;">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="approve">
              <button class="btn primary" type="submit">Odobri</button>
            </form>
            <form method="post" style="margin:0;">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="reject">
              <button class="btn" type="submit">Odbij</button>
            </form>
            <form method="post" style="margin:0;" onsubmit="return confirm('Obrisati ovu umjetninu?');">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button class="btn danger" type="submit">Obriši</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endwhile; endif; ?>
  </table>
</div>
