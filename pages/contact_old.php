<link rel="stylesheet" href="/assets/css/styles.css">
<?php $TITLE="Kontakt — DigitalnaGalerija redizajn"; 
$sent = false;
if ($_SERVER['REQUEST_METHOD']==='POST'){
  // In real deploy, send email via mail() or SMTP; here we just show a confirmation.
  $sent = true;
}
?>
<h1>Kontakt</h1>
<?php if ($sent): ?>
  <div class="notice">Hvala! Vaša poruka je zabilježena (demo). Bićete kontaktirani uskoro.</div>
<?php endif; ?>
<form method="post" class="card" style="max-width:700px">
  <div class="form-row">
    <div><label>Ime i prezime</label><input name="name" required></div>
    <div><label>Email</label><input type="email" name="email" required></div>
  </div>
  <label>Predmet</label><input name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
  <label>Poruka</label><textarea name="message" rows="6" placeholder="Kako možemo pomoći?"></textarea>
  <div style="margin-top:12px">
    <button style="background:var(--accent);border:none;padding:10px 14px;border-radius:10px;font-weight:700">Pošalji</button>
  </div>
</form>
