<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/includes/header.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }

$t = $_GET['t'] ?? '';
if (!$t) { echo '<p class="card">'.$T['db_error'].'</p>'; require __DIR__.'/includes/footer.php'; exit; }

// Fetch columns
$colsRes = $conn->query("SHOW COLUMNS FROM `".$conn->real_escape_string($t)."`");
$cols = [];
while ($c = $colsRes->fetch_assoc()) { $cols[] = $c; }
if (!$cols) { echo '<p class="card">'.$T['no_data'].'</p>'; require __DIR__.'/includes/footer.php'; exit; }
$pk = $cols[0]['Field']; // naive PK assumption

// Pagination
$per = 25;
$page = max(1, (int)($_GET['p'] ?? 1));
$off = ($page-1)*$per;
$totalRes = $conn->query("SELECT COUNT(*) AS n FROM `".$conn->real_escape_string($t)."`");
$total = (int)($totalRes->fetch_assoc()['n'] ?? 0);
$dataRes = $conn->query("SELECT * FROM `".$conn->real_escape_string($t)."` LIMIT $off,$per");
?>
<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <h2><?= htmlspecialchars($t) ?></h2>
    <a class="btn" href="tables.php">← <?= $T['back'] ?></a>
  </div>
  <div style="overflow:auto;">
    <table>
      <thead>
        <tr>
          <?php foreach ($cols as $c): ?>
            <th><?= htmlspecialchars($c['Field']) ?></th>
          <?php endforeach; ?>
          <th><?= $T['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php while ($r = $dataRes->fetch_assoc()): ?>
          <tr>
            <?php foreach ($cols as $c): $f=$c['Field']; ?>
              <td>
                <form method="post" action="update.php" style="display:inline;">
                  <input type="hidden" name="t" value="<?= htmlspecialchars($t) ?>">
                  <input type="hidden" name="pk" value="<?= htmlspecialchars($pk) ?>">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($r[$pk]) ?>">
                  <input type="hidden" name="field" value="<?= htmlspecialchars($f) ?>">
                  <input type="text" name="value" value="<?= htmlspecialchars((string)$r[$f]) ?>">
                  <button class="btn"><?= $T['save'] ?></button>
                </form>
              </td>
            <?php endforeach; ?>
            <td>
              <form method="post" action="delete.php" onsubmit="return confirm('Delete row?')">
                <input type="hidden" name="t" value="<?= htmlspecialchars($t) ?>">
                <input type="hidden" name="pk" value="<?= htmlspecialchars($pk) ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($r[$pk]) ?>">
                <button class="btn btn-danger"><?= $T['delete'] ?></button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div class="toolbar">
    <?php
    $pages = max(1, ceil($total/$per));
    for ($i=1;$i<=$pages;$i++):
      $style = $i===$page ? 'class="btn btn-primary"' : 'class="btn"';
      echo '<a '.$style.' href="?t='.urlencode($t).'&p='.$i.'">'.$i.'</a> ';
    endfor;
    ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
