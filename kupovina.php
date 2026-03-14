<?php
$conn = new mysqli("localhost", "root", "", "artsy_db");
if ($conn->connect_error) {
    die("Greška pri spajanju: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $art_id = intval($_POST['art_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);

    $sql = "INSERT INTO customers (name, email, artwork_id) VALUES ('$name', '$email', $art_id)";
    if ($conn->query($sql)) {
        echo "<p>✅ Hvala na kupovini, $name! Kontaktirat ćemo vas na $email.</p>";
        echo "<p><a href='index.php'>Natrag na početnu</a></p>";
        exit;
    } else {
        echo "Greška: " . $conn->error;
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$res = $conn->query("SELECT id, title, image_path, price FROM artworks WHERE id=$id LIMIT 1");
$art = $res->fetch_assoc();
if (!$art) {
    die("Umjetnina nije pronađena.");
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Kupovina - <?php echo htmlspecialchars($art['title']); ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <style>
    .card { max-width:400px; margin:20px auto; border:1px solid #ccc; border-radius:10px; padding:20px; text-align:center; }
    .card img { max-width:100%; border-radius:8px; }
    form { margin-top:20px; display:flex; flex-direction:column; gap:10px; }
    input, button { padding:10px; font-size:1em; }
    button { background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer; }
  </style>
</head>
<body>
<div class="card">
  <img src="<?php echo htmlspecialchars($art['image_path']); ?>" alt="<?php echo htmlspecialchars($art['title']); ?>">
  <h2><?php echo htmlspecialchars($art['title']); ?></h2>
  <p>Cijena: <?php echo number_format($art['price'], 2); ?> KM</p>

  <form method="post">
    <input type="hidden" name="art_id" value="<?php echo $art['id']; ?>">
    <input type="text" name="name" placeholder="Vaše ime" required>
    <input type="email" name="email" placeholder="Vaš email" required>
    <button type="submit">Potvrdi kupovinu</button>
  </form>
</div>
</body>
</html>
