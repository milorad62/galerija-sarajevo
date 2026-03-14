<?php
require_once __DIR__ . '/../db.php';
$res = $conn->query("SELECT id, ime, prezime, email, biografija FROM umjetnici ORDER BY id DESC");
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<title>Umjetnici — Admin panel</title>
<link rel="stylesheet" href="<?= htmlspecialchars($BASE_URL) ?>/assets/css/styles.css">
</head>
<body>
<div class="header">
  <strong>Umjetnici</strong>
  <a href="index.php" class="btn">Nazad</a>
</div>
<div class="container">
  <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;">
    <tr style="background:#0a65c0;color:#fff;">
      <th>ID</th><th>Ime</th><th>Prezime</th><th>Email</th><th>Biografija</th><th>Brisanje</th>
    </tr>
    <?php while($r=$res->fetch_assoc()): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= htmlspecialchars($r['ime']) ?></td>
      <td><?= htmlspecialchars($r['prezime']) ?></td>
      <td><?= htmlspecialchars($r['email']) ?></td>
      <td><?= nl2br(htmlspecialchars($r['biografija'])) ?></td>
      <td>
        <form method="post" action="umjetnici.php" onsubmit="return confirm('Obrisati ovog umjetnika?');">
          <input type="hidden" name="delete_id" value="<?= $r['id'] ?>">
          <button class="btn danger">Obriši</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $id = (int)$_POST['delete_id'];
  $conn->query("DELETE FROM umjetnici WHERE id=$id");
  header("Location: umjetnici.php");
}
?>
</body>
</html>
