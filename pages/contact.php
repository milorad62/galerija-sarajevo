<?php require __DIR__ . '/../config/bootstrap.php'; require __DIR__ . '/../includes/security.php'; $CFG = require __DIR__ . '/../config/app.php'; ?>
<link rel="stylesheet" href="assets/css/styles.css">

<style>
textarea {
  width: 100%;
  min-height: 180px;       /* veće polje */
  padding: 10px;
  border-radius: 8px;      /* zaobljene ivice */
  border: 1px solid #ccc;  /* svijetla ivica */
  background-color: #f9f9f9;
  font-size: 15px;
  resize: vertical;        /* korisnik može rastegnuti polje po visini */
}
textarea:focus {
  outline: none;
  border-color: #007acc;   /* plava ivica kada je aktivno */
  background-color: #fff;  /* bijela kada je aktivno */
}
</style>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Honeypot (anti-bot) - polje ne smije biti popunjeno
    if (!empty($_POST['website'])) { http_response_code(400); exit; }

    // CSRF
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        http_response_code(403);
        echo "<p style='color:red'>Sigurnosna provjera nije prošla. Osvježi stranicu i pokušaj ponovo.</p>";
        exit;
    }

    // Rate limit: 1 poruka / 30 sekundi po sesiji
    if (!rate_limit('contact', 30)) {
        echo "<p style='color:red'>Molimo sačekajte 30 sekundi prije slanja nove poruke.</p>";
        exit;
    }

    $to = $CFG['contact_to'];
    $name = clean_text($_POST["name"] ?? "", 80);
    $email = filter_var($_POST["email"] ?? "", FILTER_VALIDATE_EMAIL);
    $message = clean_text($_POST["message"] ?? "", 4000);

    if (!$email) {
        echo "<p style='color:red'>Neispravna email adresa.</p>";
        exit;
    }
    if (mb_strlen($message) < 10) {
        echo "<p style='color:red'>Poruka je prekratka. Napišite bar 10 znakova.</p>";
        exit;
    }

    $subject = "Kontakt forma — nova poruka";
    $body = "Ime: {$name}\nEmail: {$email}\n\nPoruka:\n{$message}\n\n---\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? '-') . "\nUA: " . ($_SERVER['HTTP_USER_AGENT'] ?? '-') . "\n";

    // Sigurni headeri: From je sa domena, Reply-To je korisnik
    $from = $CFG['site_email_from'];
    $headers = "From: {$CFG['site_name']} <{$from}>\r\n";
    $headers .= "Reply-To: {$name} <{$email}>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Snimi poruku lokalno (audit / evidencija)
    $msgDir = __DIR__ . '/../storage/messages';
    if (!is_dir($msgDir)) { @mkdir($msgDir, 0775, true); }
    $fname = $msgDir . '/contact_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.txt';
    @file_put_contents($fname, $body);

    if (@mail($to, $subject, $body, $headers)) {
        echo "<p style='color:green'>Poruka je uspješno poslata. Hvala!</p>";
    } else {
        echo "<p style='color:red'>Poruka je snimljena, ali slanje emaila nije uspjelo (server mail nije konfigurisan). Kontaktirajte nas direktno.</p>";
    }
}
?>

<h1>Kontaktirajte nas</h1>
<form method="POST" action="">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">

    <input type="text" name="name" placeholder="Vaše ime" required><br>
    <input type="email" name="email" placeholder="Vaš email" required><br>
    <textarea name="message" placeholder="Vaša poruka" required></textarea><br>
    <button type="submit">Pošalji</button>
</form>
