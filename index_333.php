<link rel="stylesheet" href="/assets/css/styles.css">
<?php
// Simple front controller/router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') { $path = '/'; }

$routes = [
  '/' => 'pages/home.php',
  '/galerija' => 'pages/gallery.php',
  '/gallery' => 'pages/gallery.php',
  '/umjetnici' => 'pages/artists.php',
  '/artists' => 'pages/artists.php',
  '/djelo' => 'pages/artwork.php',
  '/artwork' => 'pages/artwork.php',
  '/o-nama' => 'pages/about.php',
  '/about-us' => 'pages/about.php',
  '/kontakt' => 'pages/contact.php',
  '/contact-us' => 'pages/contact.php',
  '/faq' => 'pages/faq.php',
  '/authenticity-certificate' => 'pages/certificate.php',
  '/rent-art' => 'pages/rent.php',
];

$file = $routes[$path] ?? null;
if (!$file) {
    http_response_code(404);
    $file = 'pages/404.php';
}
require __DIR__ . '/partials/header.php';
require __DIR__ . '/' . $file;
require __DIR__ . '/partials/footer.php';
