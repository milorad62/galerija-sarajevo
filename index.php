<?php
// ===================================================
// GALERIJA SARAJEVO - CENTRALNI ROUTER
// ===================================================

// Definiši rute
$routes = [
  '/' => './pages/home.php',
  '/galerija' => './pages/gallery.php',
  '/gallery' => './pages/gallery.php',
  '/galerija/' => './pages/gallery.php',
  '/gallery/' => './pages/gallery.php',
  '/umjetnici' => './pages/artists.php',
  '/artists' => './pages/artists.php',
  '/umjetnik' => './pages/artist.php',
  '/artist' => './pages/artist.php',  
  '/djelo' => './pages/artwork.php',
  '/artwork' => './pages/artwork.php',
  '/o-nama' => './pages/about.php',
  '/about-us' => './pages/about.php',
  '/administracija' => './admin/index.php',
  '/kontakt' => './pages/contact.php',
  '/contact-us' => './pages/contact.php',
  '/faq' => './pages/faq.php',
  '/authenticity-certificate' => './pages/certificate.php',
  '/rent-art' => './pages/rent.php',
  '/artist/dashboard' => './pages/artist_dashboard.php',
  '/artist/upload' => './pages/artist_upload.php',
  '/artist/my-artworks' => './pages/artist_my_artworks.php',
  '/login' => './login.php',
  '/register' => './register.php',
  '/logout' => './logout.php',
];

// Ukloni query parametre i bazu
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
require __DIR__ . '/config/bootstrap.php';
$base = $BASE_URL; // dinamički base (npr. '' ili '/galerija_sarajevo')
$route = rtrim($path, '/');
if ($base && str_starts_with($route, $base)) { $route = substr($route, strlen($base)); }
$route = $route === '' ? '/' : $route;

// Ako ruta nije prazna, osiguraj da '/' vodi na home
if ($route === '') {
  $route = '/';
}

// Uključi header
include './partials/header.php';

// Prikaz rute
if (isset($routes[$route]) && file_exists($routes[$route])) {
  include $routes[$route];
} else {
  http_response_code(404);
  echo "<div style='padding:60px;text-align:center;'>
          <h2>Stranica ne postoji (404)</h2>
          <p>Tražena stranica nije pronađena.</p>
        </div>";
}

// Uključi footer ako postoji
if (file_exists('./partials/footer.php')) {
  include './partials/footer.php';
}
?>