<?php
$TITLE = "Galerija — Digitalna Galerija";
require __DIR__ . '/../db.php';

$q   = isset($_GET['q'])   ? trim($_GET['q'])       : '';
$min = isset($_GET['min']) ? floatval($_GET['min']) : 0;
$max = isset($_GET['max']) ? floatval($_GET['max']) : 0;

// Ako korisnik unese zarez kao decimalni separator
if (is_string($_GET['min'] ?? null)) $min = floatval(str_replace(',', '.', $_GET['min']));
if (is_string($_GET['max'] ?? null)) $max = floatval(str_replace(',', '.', $_GET['max']));

// Automatski base path: lokalno u podfolderu ili online na root-u
$BASE_URL = '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($scriptName, '/galerija_sarajevo/') !== false) {
    $BASE_URL = '/galerija_sarajevo';
}

$sql = "
SELECT u.id, u.naslov, u.opis, u.cijena, u.slika, a.ime, a.prezime
FROM umjetnine u
LEFT JOIN umjetnici a ON u.umjetnik_id = a.id
WHERE 1
";

$params = [];
$types  = '';

// tekstualna pretraga
if ($q !== '') {
    $sql .= " AND (u.naslov LIKE ? OR u.opis LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $types .= 's';
    $params[] = $like; $types .= 's';
}

// brojčani filteri
if ($min > 0) {
    $sql .= " AND u.cijena >= ?";
    $params[] = $min; $types .= 'd';
}
if ($max > 0) {
    $sql .= " AND u.cijena <= ?";
    $params[] = $max; $types .= 'd';
}

$sql .= " ORDER BY u.id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare error: " . $conn->error);
}

// dinamički bind parametara
if ($params) {
    $bind = [];
    $bind[] = &$types;
    foreach ($params as $k => $v) {
        $bind[] = &$params[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind);
}

$stmt->execute();

$arts = [];
if (method_exists($stmt, 'get_result')) {
    $result = $stmt->get_result();
    if ($result) {
        $arts = $result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    $stmt->store_result();
    $stmt->bind_result($id, $naslov, $opis, $cijena, $slika, $ime, $prezime);
    while ($stmt->fetch()) {
        $arts[] = [
            'id'      => $id,
            'naslov'  => $naslov,
            'opis'    => $opis,
            'cijena'  => $cijena,
            'slika'   => $slika,
            'ime'     => $ime,
            'prezime' => $prezime,
        ];
    }
}
$stmt->close();

// DEBUG prikaz
if (isset($_GET['debug'])) {
    echo "<pre><strong>SQL:</strong>\n$sql\n\n";
    echo "<strong>Types:</strong> $types\n";
    echo "<strong>Params:</strong> ";
    var_dump($params);
    echo "</pre>";
}
?>

<h1>Galerija</h1>

<form class="filters sticky sticky" method="get">
    <input type="text" name="q" class="input" placeholder="Traži po naslovu/opisu" value="<?= htmlspecialchars($q) ?>">
    <input type="text" inputmode="decimal" name="min" class="input input-sm" placeholder="Min KM" value="<?= $min ?: '' ?>">
    <input type="text" inputmode="decimal" name="max" class="input input-sm" placeholder="Max KM" value="<?= $max ?: '' ?>">
    <button class="btn primary" type="submit">Filtriraj</button>
    <a class="btn secondary" href="<?= $BASE_URL ?>/galerija">Reset</a>
</form>

<div class="gallery-grid">
<?php if ($arts): ?>
    <?php foreach ($arts as $art): ?>
        <?php
            $slikaFajl = !empty($art['slika']) ? ltrim($art['slika'], '/') : 'placeholder.jpg';
            $img = $BASE_URL . '/uploads/' . $slikaFajl;
            $autor = trim(($art['ime'] ?? '') . ' ' . ($art['prezime'] ?? ''));
        ?>
        <a class="art-card" href="<?= $BASE_URL ?>/pages/artwork.php?id=<?= (int)$art['id'] ?>">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($art['naslov'] ?: 'Umjetnina') ?>">
            <div class="meta">
                <div class="title"><?= htmlspecialchars($art['naslov'] ?: 'Bez naslova') ?></div>
                <div class="muted"><?= htmlspecialchars($autor) ?></div>
                <?php if ($art['cijena'] !== null): ?>
                    <div class="price"><?= number_format((float)$art['cijena'], 2, ',', '.') ?> KM</div>
                <?php endif; ?>
            </div>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <p class="muted">Trenutno nema zapisa koji odgovaraju filteru.</p>
<?php endif; ?>
</div>
