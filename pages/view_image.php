<?php
// Fallback prikaz slike (kada nije mapirana na ID iz baze)
$img = $_GET['img'] ?? '';
$img = basename($img); // sigurnost
$path = __DIR__ . '/../uploads/' . $img;
$web  = '../uploads/' . rawurlencode($img);

if (!$img || !file_exists($path)) {
  http_response_code(404);
  echo "<h2 style='font-family:Arial'>Slika nije pronađena.</h2>";
  exit;
}
?>
<!doctype html>
<html lang="bs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Umjetnina</title>
  <style>
    body{margin:0;font-family:Segoe UI,Arial;background:#111;color:#fff;}
    .wrap{max-width:1100px;margin:0 auto;padding:18px;}
    .img{width:100%;border-radius:14px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.45);}
    .img img{width:100%;height:auto;display:block;}
    a{color:#9ad;text-decoration:none}
  </style>
</head>
<body>
  <div class="wrap">
    <p><a href="../">← Nazad</a></p>
    <div class="img">
      <img src="<?= htmlspecialchars($web) ?>" alt="Umjetnina">
    </div>
  </div>
</body>
</html>
