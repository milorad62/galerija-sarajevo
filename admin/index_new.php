<?php
session_start();
require_once __DIR__ . '/../db.php';

// jednostavna provjera – možeš kasnije dodati login check
?>
<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<title>Administracija — MojaGalerija</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
body { font-family: Inter, sans-serif; margin:2rem; background:#f6f6f6; }
h1 { color:#c0392b; }
a { text-decoration:none; color:#0077aa; font-weight:600; }
table { border-collapse: collapse; width: 100%; margin-top:1rem; background:white; }
th, td { border:1px solid #ccc; padding:.5rem; text-align:left; }
tr:nth-child(even){ background:#f9f9f9; }
.btn { padding:.4rem .8rem; border-radius:6px; border:none; cursor:pointer; }
.btn-edit { background:#0077aa; color:white; }
.btn-del { background:#c0392b; color:white; }
.container { background:white; border-radius:8px; padding:1rem 2rem; }
</style>
</head>
<body>

<h1>Admin panel</h1>

<div class="container">
  <h3>Izaberi tabelu:</h3>
  <ul>
    <li><a href="?tabela=umjetnici">Umjetnici</a></li>
    <li><a href="?tabela=umjetnine">Umjetnine</a></li>
    <li><a href="?tabela=kupci">Kupci</a></li>
  </ul>

<?php
if (isset($_GET['tabela'])) {
  $tabela = preg_replace('/[^a-z_]/', '', $_GET['tabela']);
  echo "<h2>Tabela: $tabela</h2>";

  $rez = $conn->query("SELECT * FROM $tabela LIMIT 100");
  if ($rez && $rez->num_rows > 0) {
    echo "<table><tr>";
    while ($finfo = $rez->fetch_field()) echo "<th>{$finfo->name}</th>";
    echo "<th>Akcije</th></tr>";

    while ($row = $rez->fetch_assoc()) {
      echo "<tr>";
      foreach ($row as $v) echo "<td>" . htmlspecialchars($v) . "</td>";
      $id = $row['id'];
      echo "<td>
        <a class='btn btn-edit' href='edit.php?tabela=$tabela&id=$id'>Uredi</a>
        <a class='btn btn-del' href='delete.php?tabela=$tabela&id=$id' onclick='return confirm(\"Obrisati slog?\");'>Obriši</a>
      </td></tr>";
    }
    echo "</table>";
  } else {
    echo "<p>Nema slogova.</p>";
  }
}
?>
</div>
</body>
</html>
