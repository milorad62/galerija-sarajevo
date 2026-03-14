<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/header.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }
$database = $conn->query('SELECT DATABASE() AS d')->fetch_assoc()['d'] ?? 'art_gallery';
$q = $conn->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME");
$q->bind_param('s', $database);
$q->execute();
$res = $q->get_result();
?>
<div class="card">
  <h2><?= $T['tables'] ?></h2>
  <div class="toolbar">
    <form method="get">
      <input type="text" name="s" placeholder="<?= $T['search'] ?>" value="<?= htmlspecialchars($_GET['s'] ?? '') ?>">
    </form>
  </div>
  <table>
    <thead><tr><th><?= $T['table'] ?></th><th><?= $T['actions'] ?></th></tr></thead>
    <tbody>
      <?php
      $filter = strtolower(trim($_GET['s'] ?? ''));
      while ($row = $res->fetch_assoc()):
        $t = $row['TABLE_NAME'];
        if ($filter && strpos(strtolower($t), $filter) === false) continue;
      ?>
        <tr>
          <td><?= htmlspecialchars($t) ?></td>
          <td><a class="btn btn-primary" href="table_view.php?t=<?= urlencode($t) ?>"><?= $T['open'] ?></a></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
