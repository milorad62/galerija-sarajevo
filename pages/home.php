<?php
// Home stranica – carousel FEATURED umjetnina iz baze (relativne putanje, klikabilno)

// 1) Učitaj DB konekciju (očekuje se db.php u rootu projekta i $conn kao mysqli konekcija)
$dbFile = __DIR__ . '/../db.php';
$connOk = false;
if (file_exists($dbFile)) {
  require_once $dbFile;
  if (isset($conn) && $conn) { $connOk = true; }
}

// 2) Pronađi kolonu za sliku u tabeli umjetnine (podržava više naziva)
$imgCol = null;
if ($connOk) {
  $q = $conn->query("SHOW COLUMNS FROM umjetnine");
  $cols = [];
  if ($q) { while($r = $q->fetch_assoc()) { $cols[] = $r['Field']; } }
  foreach (['slika','image','filename','putanja','putanja_slike'] as $c) {
    if (in_array($c, $cols)) { $imgCol = $c; break; }
  }
}

$featured = [];
if ($connOk && $imgCol) {
  // Samo featured
  $sql = "SELECT id, `$imgCol` AS img FROM umjetnine WHERE featured = 1 ORDER BY id DESC";
  $res = $conn->query($sql);
  if ($res) {
    while ($row = $res->fetch_assoc()) {
      $file = basename($row['img']); // normalizuj (ako je u bazi putanja)
      if ($file) {
        $featured[] = [
          'id'   => (int)$row['id'],
          'file' => $file
        ];
      }
    }
  }
}
?>

<div class="home-hero">
  <?php if (!empty($featured)): ?>
    <div class="carousel-auto" aria-label="Featured carousel umjetnina">
      <div class="carousel-auto-track" id="carouselTrack">
        <?php foreach ($featured as $art): ?>
          <div class="carousel-auto-slide">
            <a class="carousel-auto-link" href="pages/artwork.php?id=<?= (int)$art['id'] ?>" aria-label="Otvori umjetninu">
              <img src="uploads/<?= htmlspecialchars(rawurlencode($art['file'])) ?>" alt="Umjetnina">
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <div style="padding: 2rem 1.2rem; text-align:center;">
      <p><b>Nema označenih featured umjetnina</b> (ili nije dostupna DB konekcija / kolona slike).</p>
    </div>
  <?php endif; ?>
</div>

<?php if (!empty($featured) && count($featured) > 1): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const track = document.getElementById('carouselTrack');
  if (!track) return;

  const slides = track.children;
  const total = slides.length;
  if (total <= 1) return;

  let index = 0;
  const intervalMs = 4000; // 4s

  function go(i){
    index = (i + total) % total;
    track.style.transform = `translateX(-${index * 100}%)`;
  }

  setInterval(() => go(index + 1), intervalMs);
});
</script>
<?php endif; ?>
