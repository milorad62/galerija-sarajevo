<?php
require_once __DIR__ . '/../db.php';

$tabela = $_GET['tabela'] ?? '';
$id = (int)($_GET['id'] ?? 0);
if (!$tabela || !$id) die("Neispravan zahtjev.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cols = [];
  foreach ($_POST as $k => $v) {
    if ($k !== 'id')
      $cols[] = "$k='" . $conn->real_escape_string($v) . "'";
  }
  $sql = "UPDATE $tabela SET " . implode(',', $cols) . " WHERE id=$id";
  $conn->query($sql);
  header("Location: index.php?tabela=$tabela");
  exit;
}

$res = $conn->query("SELECT * FROM $tabela WHERE id=$id");
$row = $res->fetch_assoc();
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<title>Uredi slog — <?= htmlspecialchars($tabela) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
body { font-family:Inter,sans-serif; background:#f5f5f5; padding:2rem; }
form { background:white; padding:1rem 2rem; border-radius:8px; max-width:500px; margin:auto; }
label { display:block; margin-top:1rem; font-weight:600; }
input, textarea { width:100%; padding:.6rem; border:1px solid #ccc; border-radius:6px; }
button { margin-top:1rem; padding:.6rem 1rem; border:none; border-radius:6px; background:#0077aa; color:white; cursor:pointer; }
</style>
</head>
<body>
<h2 style="text-align:center;">Uredi slog u tabeli: <?= htmlspecialchars($tabela) ?></h2>

<form method="post">
<?php
foreach ($row as $k => $v) {
  if ($k === 'id') {
    echo "<input type='hidden' name='id' value='".htmlspecialchars($v)."'>";
    continue;
  }
  echo "<label>$k</label>";
  if (strlen($v) > 80) {
    echo "<textarea name='$k' rows='4'>".htmlspecialchars($v)."</textarea>";
  } else {
    echo "<input type='text' name='$k' value='".htmlspecialchars($v)."'>";
  }
}
?>
  <button type="submit">💾 Sačuvaj izmjene</button>
</form>
</body>
</html>
