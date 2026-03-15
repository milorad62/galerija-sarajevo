<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Neispravan ID.");
}

$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.naslov,
        u.opis,
        u.cijena,
        u.slika,
        umjetnici.ime AS autor
    FROM umjetnine u
    LEFT JOIN umjetnici ON umjetnici.id = u.umjetnik_id
    WHERE u.id = ?
");

$stmt->execute([$id]);
$artwork = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artwork) {
    die("Djelo nije pronađeno.");
}

$slika = !empty($artwork['slika']) 
    ? '/uploads/' . ltrim($artwork['slika'], '/') 
    : '';
?>

<!DOCTYPE html>
<html lang="bs">
<head>

<meta charset="UTF-8">
<title><?php echo htmlspecialchars($artwork['naslov']); ?></title>

<style>

body{
font-family: Arial;
background:#f5f5f5;
margin:0;
}

.container{
max-width:1200px;
margin:auto;
padding:30px;
background:white;
border-radius:10px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:40px;
}

img{
max-width:100%;
border-radius:10px;
}

.title{
color:#c0392b;
font-size:34px;
margin-bottom:10px;
}

.price{
font-size:18px;
margin-top:10px;
}

.badge{
background:#070b1a;
color:white;
padding:20px;
border-radius:10px;
margin-top:20px;
}

form input,
form textarea{
width:100%;
padding:10px;
margin-top:10px;
border:1px solid #ddd;
border-radius:6px;
}

button{
margin-top:10px;
padding:10px 20px;
background:#c0392b;
color:white;
border:none;
border-radius:6px;
cursor:pointer;
}

</style>

</head>
<body>

<div class="container">

<div class="grid">

<div>

<?php if ($slika): ?>

<img src="<?php echo htmlspecialchars($slika); ?>" 
alt="<?php echo htmlspecialchars($artwork['naslov']); ?>">

<?php else: ?>

<p>Slika nije dostupna.</p>

<?php endif; ?>

</div>


<div>

<h1 class="title">
<?php echo htmlspecialchars($artwork['naslov']); ?>
</h1>

<p>
<?php echo htmlspecialchars($artwork['opis']); ?>
</p>

<p class="price">
<strong>Cijena:</strong> 
<?php echo number_format($artwork['cijena'],2,",","."); ?> KM
</p>

<p>
<strong>Autor:</strong> 
<?php echo htmlspecialchars($artwork['autor']); ?>
</p>

<div class="badge">
Certifikat autentičnosti dostupan pri kupovini.
</div>

<h3>Pošaljite upit za ovo djelo</h3>

<form>

<label>Vaše ime</label>
<input type="text">

<label>Email</label>
<input type="email">

<label>Poruka</label>
<textarea>Zanima me dostupnost i cijena za "<?php echo htmlspecialchars($artwork['naslov']); ?>"</textarea>

<button type="submit">Pošalji</button>

</form>

</div>

</div>

</div>

</body>
</html>
