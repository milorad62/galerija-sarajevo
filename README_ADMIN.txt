# Admin podfolder za galerija_sarajevo

Putanja: `htdocs/galerija_sarajevo/admin`

## Koraci
1. Kopiraj folder `admin/` u `htdocs/galerija_sarajevo/` (ako već nije tu).
2. U bazi `art_gallery` pokreni `make_users_admin.sql` (dodaje kolonu `role` i postavi sebi `role='admin'`).
3. Otvori `http://localhost/galerija_sarajevo/admin/`.
4. Prijavi se istim korisničkim nalogom kao na glavnom sajtu.
5. Jezik mijenjaš preko `?lang=bs` ili `?lang=en`.

### Funkcionalnosti
- Umjetnik (`role=artist`) dodaje umjetnine: `http://localhost/galerija_sarajevo/pages/dodaj_umjetninu.php`
- Admin (role=admin) vidi sve tabele i uređuje/brise slogove.

> Napomena: Generički editor koristi **prvu kolonu** kao primarni ključ. Ako tabela ima drukčiji PK, prilagodite u `table_view.php`.
