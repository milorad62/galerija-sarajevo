
<!-- ======= CAROUSEL UMJETNINA ======= -->
<div class="carousel-container">
    <button class="carousel-button prev">&#10094;</button>
    <div class="carousel-track">
        <?php
        include 'db.php';
        $result = $conn->query("SELECT id, slika FROM umjetnine ORDER BY id DESC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = htmlspecialchars($row['id']);
                $slika = htmlspecialchars($row['slika']);
                echo "<div class='carousel-item'>
                        <a href='djelo.php?id=$id'>
                            <img src='uploads/$slika' alt='Umjetnina $id'>
                        </a>
                      </div>";
            }
        } else {
            echo "<p style='text-align:center; width:100%;'>Nema dostupnih umjetnina.</p>";
        }
        ?>
    </div>
    <button class="carousel-button next">&#10095;</button>
</div>

<style>
.carousel-container {
    position: relative;
    width: 100%;
    margin: 0 auto;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    background-color: #fff;
}
.carousel-track {
    display: flex;
    transition: transform 0.5s ease-in-out;
}
.carousel-item {
    min-width: 100%;
    box-sizing: border-box;
}
.carousel-item img {
    width: 100%;
    height: 480px;
    object-fit: cover;
}
.carousel-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0,0,0,0.5);
    border: none;
    color: #fff;
    font-size: 30px;
    padding: 8px 14px;
    cursor: pointer;
    border-radius: 50%;
    z-index: 5;
}
.carousel-button:hover {
    background-color: rgba(0,0,0,0.8);
}
.carousel-button.prev { left: 15px; }
.carousel-button.next { right: 15px; }
</style>

<script>
const track = document.querySelector('.carousel-track');
let slides = [];
let nextButton, prevButton;
let currentSlide = 0;

window.addEventListener('DOMContentLoaded', () => {
    slides = Array.from(track.children);
    nextButton = document.querySelector('.carousel-button.next');
    prevButton = document.querySelector('.carousel-button.prev');

    function moveToSlide(index) {
        track.style.transform = `translateX(-${index * 100}%)`;
        currentSlide = index;
    }

    nextButton.addEventListener('click', () => {
        if (currentSlide < slides.length - 1) {
            moveToSlide(currentSlide + 1);
        } else {
            moveToSlide(0);
        }
    });

    prevButton.addEventListener('click', () => {
        if (currentSlide > 0) {
            moveToSlide(currentSlide - 1);
        } else {
            moveToSlide(slides.length - 1);
        }
    });

    setInterval(() => {
        if (slides.length > 0) {
            let next = (currentSlide + 1) % slides.length;
            moveToSlide(next);
        }
    }, 3000);
});
</script>

<link rel="stylesheet" href="./assets/css/styles.css">
<?php
// Simple front controller/router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') { $path = '/'; }

$routes = [
  '/' => './pages/home.php',
  '/galerija' => './pages/gallery.php',
  '/gallery' => './pages/gallery.php',
  '/umjetnici' => './pages/artists.php',
  '/artists' => './pages/artists.php',
  '/djelo' => './pages/artwork.php',
  '/artwork' => './pages/artwork.php',
  '/o-nama' => './pages/about.php',
  '/about-us' => './pages/about.php',
  '/kontakt' => './pages/contact.php',
  '/contact-us' => './pages/contact.php',
  '/faq' => './pages/faq.php',
  '/authenticity-certificate' => './pages/certificate.php',
  '/rent-art' => './pages/rent.php',
];

$file = $routes[$path] ?? null;
if (!$file) {
    http_response_code(404);
    $file = 'pages/404.php';
}
require __DIR__ . '/partials/header.php';
require __DIR__ . '/' . $file;
require __DIR__ . '/partials/footer.php';
