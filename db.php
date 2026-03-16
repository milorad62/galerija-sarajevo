<?php

$DB_HOST = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'art_gallery';
$DB_USER = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '';
$DB_PORT = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("DB Connection error: " . $e->getMessage());
}

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);

if (!$conn) {
    die("MySQLi Connection error: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
