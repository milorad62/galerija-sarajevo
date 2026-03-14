# DigitalnaGalerija Redizajn (PHP)

Ovaj paket je dorada tvog projekta da vizuelno i funkcionalno liči na digitalnagalerija.ba.

## Brzi start (lokalno)
1. Importuj `art_gallery (1).sql` u MySQL/MariaDB.
2. Podesi `.env` ili sistemske varijable (opciono) ili izmijeni `db.php` sa svojim kredencijalima.
3. Stavi cijeli folder na PHP server (npr. XAMPP `htdocs`).
4. Otvori `http://localhost/`.

## Rute
- `/` — početna (hero + istaknuta djela)
- `/galerija` — listanje djela sa filtrima (pretraga, min/max cijena)
- `/djelo?id=ID` — detalji djela + kontakt upit
- `/umjetnici` — lista umjetnika
- `/authenticity-certificate` — info o certifikatu
- `/faq` — pitanja i odgovori
- `/o-nama` — o nama
- `/kontakt` — kontakt forma (demo potvrda)

## Napomena
- Upload slike očekujemo u `uploads/` (putanja u bazi `umjetnine.slika`).
- E-mail slanje je simulirano radi demoa — u produkciji koristite SMTP.
