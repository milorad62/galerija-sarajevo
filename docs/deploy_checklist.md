# Deployment checklist (komercijalna verzija)

## 1) Konfiguracija
- [ ] U `emails.php` upiši stvarne adrese: kontakt/prodaja/podrska
- [ ] U `db.php` postavi produkcijske kredencijale (host, user, pass, db)
- [ ] (Opcionalno) Postavi env var `APP_ENV=production` na hostingu

## 2) Sigurnost
- [ ] Provjeri da `.htaccess` radi (Options -Indexes, blokada db.php/emails.php)
- [ ] Onemogući `display_errors` u produkciji (radi preko `config/bootstrap.php`)
- [ ] Provjeri upload validacije (tip, veličina, ekstenzija)

## 3) Email slanje
- [ ] Ako hosting ne podržava `mail()`, koristi SMTP (PHPMailer) – možemo dodati
- [ ] Testiraj kontakt formu: provjera CSRF, rate limit, zapis u `storage/messages`

## 4) Performanse
- [ ] Indeks na `featured`: `ALTER TABLE umjetnine ADD INDEX(featured);`
- [ ] Optimizuj slike (WebP, kompresija, dimenzije)

## 5) Pravno/komercijalno
- [ ] Dodaj: Politika privatnosti, Uslovi korištenja, Cookie obavijest
- [ ] Dodaj: Kontakt podaci firme (adresa, ID broj, PDV ako treba)

