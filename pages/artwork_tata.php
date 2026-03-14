<link rel="stylesheet" href="/assets/css/styles.css">
<?php $TITLE="Djelo — DigitalnaGalerija redizajn"; require __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0);
$art = null;
if ($id>0){
  $stmt = $pdo->prepare("SELECT u.*, a.ime, a.prezime FROM umjetnine u LEFT JOIN umjetnici a ON u.umjetnik_id=a.id WHERE u.id=?");
  $stmt->execute([$id]); $art = $stmt->fetch();
}
if (!$art){ echo "<h1>Djelo nije pronađeno</h1>"; return; }
$img = '/uploads/' . ($art['slika'] ?: 'placeholder.jpg');

// === Slanje upita direktno iz djelo.php ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_inquiry'])) {
    $emails = require __DIR__ . "/../emails.php";
    $to = $emails['prodaja'];   // adresa za prodaju

    $name = strip_tags($_POST["name"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $message = strip_tags($_POST["message"]);
    $subject = "Upit za djelo: " . $art['naslov'];

    $body = "Ime: $name\nEmail: $email\n\nPoruka:\n$message";
    $headers = "From: $email\r\nReply-To: $email\r\n";

    if ($email && mail($to, $subject, $body, $headers)) {
        echo "<p style='color:green'>Vaš upit je uspješno poslan.</p>";
    } else {
        echo "<p style='color:red'>Došlo je do greške pri slanju poruke.</p>";
    }
}
?>
<div class="grid cols-3">
  <div class="card" style="grid-column: span 2">
    <img src="<?= htmlspecialchars($img) ?>" alt="art" style="width:100%;height:auto;border-radius:14px">
  </div>
  <div class="card">
    <h2><?= htmlspecialchars($art['naslov'] ?: 'Bez naslova') ?></h2>
    <p class="muted"><?= nl2br(htmlspecialchars($art['opis'] ?: '')) ?></p>
    <?php if ($art['cijena']): ?><p class="price">Cijena: <strong><?= number_format($art['cijena'],2,',','.') ?> KM</strong></p><?php endif; ?>
    <p>Autor: <strong><?= htmlspecialchars(($art['ime'] ?? '').' '.($art['prezime'] ?? '')) ?></strong></p>
    <div class="notice">Certifikat autentičnosti dostupan pri kupovini. <a href="/authenticity-certificate">Saznaj više</a>.</div>

    <h3>Pošaljite upit za ovo djelo</h3>
    <form method="post" action="">
      <label>Vaše ime</label><input name="name" required>
      <label>Email</label><input type="email" name="email" required>
      <label>Poruka</label>
      <textarea name="message" rows="6" style="width:100%" required>
Zanima me dostupnost i cijena za "<?= htmlspecialchars($art['naslov']) ?>".
      </textarea>
      <div style="margin-top:8px">
        <button type="submit" name="send_inquiry" style="background:var(--accent);border:none;padding:10px 14px;border-radius:10px;font-weight:700">Pošalji upit</button>
      </div>
    </form>
  </div>
</div>

<p>
  <a class="badge" href="/pages/artist.php?id=<?= $art['umjetnik_id'] ?>">← Nazad na umjetnika</a>
</p>
