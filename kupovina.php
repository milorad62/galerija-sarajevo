<?php
require __DIR__ . '/db.php';

$art_id = isset($_GET['art_id']) ? (int)$_GET['art_id'] : 0;

$stmt = $conn->prepare("
SELECT u.id, u.naslov, u.slika, u.cijena, a.ime, a.prezime
FROM umjetnine u
LEFT JOIN umjetnici a ON u.umjetnik_id = a.id
WHERE u.id = ?
LIMIT 1
");

$stmt->bind_param("i", $art_id);
$stmt->execute();
$res = $stmt->get_result();
$art = $res->fetch_assoc();
$stmt->close();

if (!$art) {
    die("Umjetnina nije pronađena.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $ime   = trim($_POST['ime']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("
        INSERT INTO kupci (ime, email, art_id)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("ssi", $ime, $email, $art_id);

    if ($stmt->execute()) {

        echo "<p>✅ Hvala na interesu, $ime!</p>";
        echo "<p>Kontaktirat ćemo vas na $email.</p>";
        echo "<p><a href='index.php'>Natrag na galeriju</a></p>";
        exit;

    } else {

        echo "Greška: " . $stmt->error;

    }

    $stmt->close();
}

$slika = "/uploads/" . $art['slika'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Kupovina - <?= htmlspecialchars($art['naslov']) ?></title>

<style>
.card{
max-width:420px;
margin:40px auto;
border:1px solid #ccc;
border-radius:10px;
padding:20px;
text-align:center;
font-family:Arial;
}

.card img{
max-width:100%;
border-radius:8px;
}

form{
margin-top:20px;
display:flex;
flex-direction:column;
gap:10px;
}

input,button{
padding:10px;
font-size:16px;
}

button{
background:#28a745;
color:white;
border:none;
border-radius:6px;
cursor:pointer;
}
</style>

</head>

<body>

<div class="card">

<img src="<?= htmlspecialchars($slika) ?>">

<h2><?= htmlspecialchars($art['naslov']) ?></h2>

<p>
Autor:
<?= htmlspecialchars($art['ime'] . ' ' . $art['prezime']) ?>
</p>

<p>
Cijena:
<?= number_format($art['cijena'],2,",",".") ?> KM
</p>

<form method="post">

<input type="hidden" name="art_id" value="<?= $art['id'] ?>">

<input type="text" name="ime" placeholder="Vaše ime" required>

<input type="email" name="email" placeholder="Vaš email" required>

<button type="submit">
Pošalji upit
</button>

</form>

</div>

</body>
</html>
