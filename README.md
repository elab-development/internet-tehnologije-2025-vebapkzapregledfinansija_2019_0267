# Veb aplikacija za pregled licnih finansija

Ovaj projekat je razvijen za potrebe predmeta **Internet tehnologije** na Fakultetu organizacionih nauka.  
Cilj aplikacije je da omogući korisnicima pregled i upravljanje ličnim finansijama kroz web interfejs.

## 📦 Tehnologije
- **Laravel (PHP)** – backend API
- **React (Node.js)** – frontend aplikacija
- **MySQL** – baza podataka
- **Docker Compose** – orkestracija servisa
- **Railway** – cloud platforma za produkcionu bazu, frontend i backend deployment

## ⚙️ Instalacija
Pre nego što pokreneš projekat, potrebno je da instaliraš:
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Git](https://git-scm.com/downloads)

## 🚀 Pokretanje projekta
1. Kloniraj repozitorijum:
    ```bash
    git clone https://github.com/elab-development/internet-tehnologije-2025-vebapkzapregledfinansija_2019_0267.git
    cd internet-tehnologije-2025-vebapkzapregledfinansija_2019_0267
2. Pokreni Docker kontejnere:
    ```bash
    docker compose up -d --build
3. Laravel backend će biti dostupan na:
     ``` 
     http://localhost/api
     
4. React frontend za produkciju će biti dostupan na:
     ```
     http://localhost

5. React frontend za development će biti dostupan na:
     ```
     http://localhost:3000

Odradjeno zbog Hotreloada, jer localhost preko nginx-a trazi rebuild svaki put kad se unese promena na frontendu. Zato svaki put kad se menja nesto na frontendu obavezno ponovo buildovati projekat nakon zavrsetka rada.

## 🌐 Produkciono okruženje (Deployment)

Aplikacija je uspešno hostovana na **Railway** platformi i podeljena je na tri nezavisna servisa koji komuniciraju u klaudu:

- 🖥️ **Frontend (Production):** https://amused-balance-production-9d66.up.railway.app/
- ⚙️ **Backend API:** https://internet-tehnologije-2025-vebapkzapregledfinansi-production.up.railway.app/api
- 🗄️ **Baza podataka:** MySQL produkciona baza hostovana u okviru istog Railway projekta

### 🔑 Produkciona podešavanja (Environment Variables)
Za pokretanje u produkciji, na Railway platformi su konfigurisane sledeće ključne varijable:
- `APP_ENV=production` & `APP_DEBUG=false` (za stabilnost i bezbednost Laravel-a)
- `APP_KEY` (generisan unikatni kriptografski ključ)
- `VIEW_COMPILED_PATH=/tmp` (eksplicitno podešena putanja za skladištenje kompajliranih Blade šablona unutar privremenog skladišta kontejnera)
- `DB_CONNECTION=mysql` (konekcija usmerena na MySQL servis)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (varijable koje bezbedno povezuju Laravel backend sa MySQL servisom koristeći Railway reference (`${{MySQL.*}}`))


## 🔑 Podešavanja
- U .env fajlu za Laravel podesi:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=finansije
    DB_USERNAME=laravel
    DB_PASSWORD=laravel
- Zatim pokrenuti komande:
    ```bash
    docker compose exec laravel php artisan key:generate
    docker compose exec laravel php artisan config:clear
    docker compose exec laravel php artisan migrate
    docker compose exec laravel php artisan db:seed

- Ako ne radi prva naredba, pokrenuti sledece:
    ```bash
    docker compose exec laravel composer install

- Frontend koristi Axios sa baseURL:
    ```js
    baseURL: 'http://localhost/api'

- Proveri bazu podataka kroz DatabaseClient ekstenziju na Visual Studio Code koristeći:
    - Host: localhost
    - Port: 3306
    - Database: finansije
    - User: laravel
    - Password: laravel


## 📋 Funkcionalnosti
- Registracija i prijava korisnika (JWT tokeni)
- Pregled kategorija prihoda i rashoda
- Dodavanje i izmena transakcija
- Pregled statistike finansija
- Seed podaci za testiranje (korisnici, kategorije, transakcije)

##  API Dokumentacija (Swagger UI)
Za lakše testiranje i pregled API endpointe-a, implementiran je Swagger UI. Dokumentacija se automatski generiše na osnovu anotacija u kontrolerima i omogućava interaktivno testiranje ruti direktno iz pregledača.

Swagger UI je dostupan na adresi:
http://localhost/api/api-documentation

##  Autorizacija
Kako bi pristupila zaštićenim rutama (poput unosa transakcija ili pregleda budžeta), potrebno je da se autorizuješ kroz Swagger interfejs:

- Klikni na dugme "Authorize" u gornjem desnom uglu Swagger stranice.
- Unesi svoj Bearer token (ili se uloguj kroz API da bi dobila token).
- Nakon toga, svi zahtevi ka zaštićenim rutama će automatski sadržati potreban autorizacioni header.

## 🧪 Testiranje

- Za pokretanje automatizovanih testova pokrenuti naredbu u terminalu:
    ```bash
    docker compose exec laravel php artisan test


📖 Ovaj README služi kao vodič za instalaciju i pokretanje projekta, kao i pregled osnovnih funkcionalnosti aplikacije.
