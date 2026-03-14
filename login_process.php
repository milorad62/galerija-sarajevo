<link rel="stylesheet" href="/assets/css/styles.css">
<?php
session_start();
require_once "db_connection.php";

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

// ---- PROVJERA UMJETNIKA ----
$sql = "SELECT id, name, password FROM artists WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['artist_id'] = $row['id'];
        $_SESSION['artist_name'] = $row['name'];
        header("Location: artist_dashboard.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Pogrešna lozinka.";
        header("Location: login.php");
        exit;
    }
}

// ---- PROVJERA ADMINA ----
$sql = "SELECT id, username, password FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        header("Location: admin/dashboard.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Pogrešna lozinka.";
        header("Location: login.php");
        exit;
    }
}

// ---- PROVJERA KUPCA ----
$sql = "SELECT id, name, password FROM customers WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['customer_id'] = $row['id'];
        $_SESSION['customer_name'] = $row['name'];
        header("Location: customer_dashboard.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Pogrešna lozinka.";
        header("Location: login.php");
        exit;
    }
}

$_SESSION['login_error'] = "Korisnik ne postoji.";
header("Location: login.php");
exit;
?>