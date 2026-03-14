<?php
// db.php - database connection settings
$DB_HOST = getenv('DB_HOST') ?: 'sql302.infinityfree.com';
$DB_NAME = getenv('DB_NAME') ?: 'if0_41280102_art_gallery';
$DB_USER = getenv('DB_USER') ?: 'if0_41280102';
$DB_PASS = getenv('DB_PASS') ?: 'HfLUAbqj48bT';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB Connection error: " . htmlspecialchars($e->getMessage());
    exit;
}
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$conn) {
    http_response_code(500);
    echo "MySQLi Connection error: " . htmlspecialchars(mysqli_connect_error());
    exit;
}
mysqli_set_charset($conn, "utf8mb4");
