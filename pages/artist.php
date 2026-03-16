<?php
$TITLE = "Umjetnik — Digitalna Galerija";
require __DIR__ . '/../db.php';

$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<h1>Umjetnik nije pronađen</h1>";
    return;
}

$stmt = $conn->prepare("SELECT id, ime, prezime, email, biografija, web, instagram, facebook FROM umjetnici WHERE id = ?");
if (!$stmt) {
    die("SQL prepare error: " . $conn->error);
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$artist) {
    echo "<h1>Umjetnik nije pronađen</h1>";
    return;
}

// Provjera da li postoji kolona status
$hasStatus = false;
$colRes = $conn->query("SHOW COLUMNS FROM umjetnine LIKE 'status'");
if ($colRes && $colRes->num_rows > 0) {
    $hasStatus = true;
}

$sql = "SELECT id, naslov, opis, cijena, slika FROM umjetnine WHERE umjetnik_id = ?";
if ($hasStatus) {
    $sql .= " AND status = 'approved'";
}
$sql .= " ORDER BY id DESC";

$st = $conn->prepare($sql);
if (!$st) {
    die("SQL prepare error: " . $conn->error);
}

$st->bind_param('i', $id);
$st->execute();
$res = $st->get_result();

$artworks = [];
if ($res) {
    $artworks = $res->fetch_all(MYSQLI_ASSOC);
}
$st->close();
?>

<div class="card" style="margin-bottom: 22px;">
    <h1><?= htmlspecialchars(($artist['ime'] ?? '') . ' ' . ($artist['prezime'] ?? '')) ?></h1>

    <?php if (!empty($artist['biografija'])): ?>
        <p class="muted"><?= nl2br(htmlspecialchars($artist['biografija'])) ?></p>
    <?php endif; ?>

    <div class="row" style="gap:12px; flex-wrap:wrap; margin-top: 10px;">
        <?php if (!empty($artist['web'])): ?>
            <a class="chip" href="<?= htmlspecialchars($artist['web']) ?>" target="_blank" rel="noopener">Web</a>
        <?php endif; ?>

        <?php if (!empty($artist['instagram'])): ?>
            <a class="chip" href="<?= htmlspecialchars($artist['instagram']) ?>" target="_blank" rel="noopener">Instagram</a>
        <?php endif; ?>

        <?php if (!empty($artist['facebook'])): ?>
            <a class="chip" href="<?= htmlspecialchars($artist['facebook']) ?>" target="_blank" rel="noopener">Facebook</a>
        <?php endif; ?>

        <?php if (!empty($artist['email'])): ?>
            <a class="chip" href="<?= htmlspecialchars($BASE_URL) ?>/kontakt">Kontakt galerije</a>
        <?php endif; ?>
    </div>
</div>

<h2>Djela umjetnika</h2>

<?php if (!empty($artworks)): ?>
    <div class="grid cols-3">
        <?php foreach ($artworks as $art): ?>
            <?php
                $slikaFajl = !empty($art['slika']) ? ltrim($art['slika'], '/') : 'placeholder.jpg';
                $img = $BASE_URL . '/uploads/' . $slikaFajl;
            ?>
            <div class="card">
                <a href="<?= htmlspecialchars($BASE_URL) ?>/pages/artwork.php?id=<?= (int)$art['id'] ?>" style="text-decoration:none;color:inherit;">
                    <div class="title"><?= htmlspecialchars($art['naslov'] ?: 'Bez naslova') ?></div>

                    <img
                        src="<?= htmlspecialchars($img) ?>"
                        style="max-width:100%;height:auto;border-radius:10px;"
                        alt="<?= htmlspecialchars($art['naslov'] ?: 'Umjetnina') ?>"
                    >

                    <?php if (!empty($art['opis'])): ?>
                        <p class="muted"><?= htmlspecialchars($art['opis']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($art['cijena'])): ?>
                        <div class="muted"><?= number_format((float)$art['cijena'], 2, ',', '.') ?> KM</div>
                    <?php endif; ?>
                </a>

                <div style="margin-top:10px;">
                    <a class="btn primary" href="<?= htmlspecialchars($BASE_URL) ?>/kupovina.php?art_id=<?= (int)$art['id'] ?>">Pošalji upit</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Ovaj umjetnik trenutno nema objavljenih djela.</p>
<?php endif; ?>
