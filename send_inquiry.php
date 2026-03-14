<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ime = trim($_POST['ime']);
  $email = trim($_POST['email']);
  $poruka = trim($_POST['poruka']);
  $artwork = $_POST['artwork_title'] ?? '';
  $artist = $_POST['artist_name'] ?? '';

  $to = "kontakt@mojagalerija.ba"; // promijeni kasnije ako imaš centralni mail
  $subject = "Upit za djelo: $artwork";
  $body = "Pošiljalac: $ime <$email>\n"
        . "Umjetnik: $artist\n"
        . "Djelo: $artwork\n\n"
        . "Poruka:\n$poruka";
  $headers = "From: $email\r\nReply-To: $email\r\n";

  if (mail($to, $subject, $body, $headers)) {
    echo "<script>alert('Vaš upit je uspješno poslan!'); window.history.back();</script>";
  } else {
    echo "<script>alert('Greška pri slanju. Pokušajte ponovo.'); window.history.back();</script>";
  }
}
?>
