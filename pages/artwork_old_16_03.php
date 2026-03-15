<?php
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("
  SELECT u.id, u.naslov, u.opis, u.cijena, u.slika, a.ime, a.prezime 
  FROM umjetnine u 
  LEFT JOIN umjetnici a ON u.umjetnik_id = a.id 
  WHERE u.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$djelo = $result->fetch_assoc();

if (!$djelo) {
  echo "<h2 style='text-align:center;'>Djelo nije pronađeno.</h2>";
  exit;
}
?>
<!doctype html>
<html lang="bs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($djelo['naslov']) ?> — MojaGalerija.ba</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="http://localhost/galerija_sarajevo/assets/css/styles.css">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      display: flex;
      gap: 2rem;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      padding: 1.5rem;
    }

    .image-box {
      flex: 1.2;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .image-box img {
      width: 100%;
      max-height: 600px;
      object-fit: cover;
      border-radius: 12px;
    }

    .details-box {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }

    .details-box h2 {
      font-size: 2rem;
      color: #c0392b;
      margin-bottom: .5rem;
    }

    .details-box p {
      margin: .3rem 0;
      color: #333;
    }

    .details-box strong {
      font-weight: 600;
    }

    .auth-box {
      background: #0a0a18;
      color: white;
      padding: 1rem;
      border-radius: 10px;
      margin: 1rem 0;
    }

    .contact-form {
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: .5rem;
    }

    .contact-form input,
    .contact-form textarea {
      padding: .6rem;
      border-radius: 6px;
      border: 1px solid #ccc;
      width: 100%;
      font-family: 'Inter', sans-serif;
      font-size: 1rem;
    }

    .contact-form button {
      background: #0077aa;
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 6px;
      padding: .7rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .contact-form button:hover {
      background: #005f85;
    }

    header.site-header {
      background: #333;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    header a {
      color: white;
      text-decoration: none;
      font-weight: 600;
    }

    header .brand {
      font-size: 1.5rem;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
      }
      .image-box img {
        max-height: 400px;
      }
    }
  </style>
</head>

<body>

<header class="site-header">
  <a href="http://localhost/galerija_sarajevo/" class="brand">MojaGalerija.ba</a>
  <nav>
    <a href="http://localhost/galerija_sarajevo/galerija">Galerija</a>
    <a href="http://localhost/galerija_sarajevo/umjetnici">Umjetnici</a>
    <a href="http://localhost/galerija_sarajevo/authenticity-certificate">Certifikat</a>
    <a href="http://localhost/galerija_sarajevo/faq">FAQ</a>
    <a href="http://localhost/galerija_sarajevo/o-nama">O nama</a>
    <a href="http://localhost/galerija_sarajevo/kontakt">Kontakt</a>
  </nav>
</header>

<div class="container">
  <div class="image-box">
    <img src="/galerija_sarajevo/uploads/<?= htmlspecialchars($djelo['slika']) ?>" alt="<?= htmlspecialchars($djelo['naslov']) ?>">
  </div>

  <div class="details-box">
    <h2><?= htmlspecialchars($djelo['naslov']) ?></h2>
    <p><?= nl2br(htmlspecialchars($djelo['opis'])) ?></p>
    <p><strong>Cijena:</strong> <?= number_format((float)$djelo['cijena'], 2, ',', '.') ?> KM</p>
    <p><strong>Autor:</strong> <?= htmlspecialchars($djelo['ime'] . ' ' . $djelo['prezime']) ?></p>

    <div class="auth-box">
      <strong>Certifikat autentičnosti</strong> dostupan pri kupovini.<br>
      <a href="http://localhost/galerija_sarajevo/certifikat" style="color:#66c2ff;">Saznaj više.</a>
    </div>

    <h4 style="color:#c0392b;">Pošaljite upit za ovo djelo</h4>
    <form method="post" action="/galerija_sarajevo/send_inquiry.php" class="contact-form">
      <input type="hidden" name="artwork_id" value="<?= htmlspecialchars($djelo['id']) ?>">
      <input type="hidden" name="artwork_title" value="<?= htmlspecialchars($djelo['naslov']) ?>">
      <input type="hidden" name="artist_name" value="<?= htmlspecialchars($djelo['ime'].' '.$djelo['prezime']) ?>">

      <label for="ime">Vaše ime</label>
      <input type="text" id="ime" name="ime" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="poruka">Poruka</label>
      <textarea id="poruka" name="poruka" rows="4" required>Zanima me dostupnost i cijena za "<?= htmlspecialchars($djelo['naslov']) ?>"</textarea>

      <button type="submit" class="btn primary" style="margin-top:10px;">📨 Pošalji upit</button>
    </form>
  </div>
</div>

</body>
</html>
