<?php
// Home stranica – prikaz carousel-a umjetnina

include __DIR__ . '/../db.php'; // sigurna apsolutna putanja prema bazi
?>

<div class="carousel-container">
  <button class="carousel-button prev">&#10094;</button>
  <div class="carousel-track">
    <?php
    $query = "SELECT id, slika FROM umjetnine ORDER BY id DESC";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = (int)$row['id'];
            $slika = htmlspecialchars($row['slika']);
            $putanja = "/galerija_sarajevo/uploads/" . $slika;

            echo "
            <div class='carousel-item'>
              <a href='/galerija_sarajevo/djelo?id=$id'>
                <img src='$putanja' alt='Umjetnina $id'>
              </a>
            </div>";
        }
    } else {
        echo '<p style=\"text-align:center;width:100%;\">Nema dostupnih umjetnina.</p>';
    }
    ?>
  </div>
  <button class="carousel-button next">&#10095;</button>
</div>

<style>
.carousel-container {
  position: relative;
  width: 90%;
  margin: 40px auto;
  overflow: hidden;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease;
}

.carousel-item {
  flex: 0 0 calc(100% / 3); /* prikaz 3 slike istovremeno */
  box-sizing: border-box;
  padding: 10px;
}

.carousel-item img {
  width: 100%;
  height: 350px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
  cursor: pointer;
  background-color: #f2f2f2;
}

.carousel-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0,0,0,0.5);
  color: white;
  border: none;
  cursor: pointer;
  padding: 10px;
  border-radius: 50%;
  font-size: 22px;
  z-index: 2;
}

.carousel-button.prev { left: 15px; }
.carousel-button.next { right: 15px; }

.carousel-button:hover {
  background-color: rgba(0,0,0,0.7);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const track = document.querySelector('.carousel-track');
  const items = Array.from(track.children);
  const nextButton = document.querySelector('.carousel-button.next');
  const prevButton = document.querySelector('.carousel-button.prev');
  let index = 0;
  const visible = 3;

  function updateCarousel() {
    const width = items[0].getBoundingClientRect().width;
    track.style.transform = `translateX(-${index * width}px)`;
  }

  nextButton.addEventListener('click', () => {
    if (index < items.length - visible) index++;
    else index = 0;
    updateCarousel();
  });

  prevButton.addEventListener('click', () => {
    if (index > 0) index--;
    else index = items.length - visible;
    updateCarousel();
  });
});
</script>
