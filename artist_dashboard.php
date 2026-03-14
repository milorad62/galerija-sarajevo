<?php
session_start();
if (!isset($_SESSION['artist_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <link rel="stylesheet" href="/assets/css/styles.css">

    <meta charset="UTF-8">
    <title>Artist Dashboard</title>
    
</head>
<body class="dashboard">
    <div class="container">
        <h1>Dobrodošli, <?php echo htmlspecialchars($_SESSION['artist_name']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="upload_artwork.php">➕ Upload novog rada</a></li>
                <li><a href="my_artworks.php">🎨 Pregled mojih radova</a></li>
                <li><a href="logout.php">🚪 Odjava</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>