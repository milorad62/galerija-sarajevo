<!doctype html>
<html lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Test CSS — Galerija Sarajevo</title>

<!-- 🔹 ISTA PUTANJA kao u dodaj_umjetninu.php -->
<link rel="stylesheet" href="../assets/css/styles.css">

<style>
/* Ako CSS nije pronađen, ovo će biti fallback stil (crveni okvir) */
.test {
  margin: 3rem auto;
  width: 400px;
  text-align: center;
  font-size: 1.2rem;
  font-weight: 600;
  padding: 1rem;
  border: 2px solid red;
  color: red;
}
</style>
</head>
<body>

<div class="test">CSS se nije učitao ❌</div>

<script>
// Ako se CSS uspješno učita, promijeni boju i tekst
window.addEventListener("load", () => {
  const testDiv = document.querySelector(".test");
  const style = getComputedStyle(testDiv);
  // provjeri da li postoji pravilo iz styles.css (npr. font-family)
  if (style.fontFamily.includes("Inter") || style.borderRadius) {
    testDiv.style.border = "3px solid #0077b6";
    testDiv.style.color = "#0077b6";
    testDiv.textContent = "CSS učitan ispravno ✅";
  }
});
</script>

</body>
</html>

