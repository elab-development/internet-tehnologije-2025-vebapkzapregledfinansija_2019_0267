# Veb aplikacija za pregled licnih finansija

Ovaj projekat je razvijen za potrebe predmeta **Internet tehnologije** na Fakultetu organizacionih nauka.  
Cilj aplikacije je da omogući korisnicima pregled i upravljanje ličnim finansijama kroz web interfejs.

## 📦 Tehnologije
- **Laravel (PHP)** – backend API
- **React (Node.js)** – frontend aplikacija
- **MySQL** – baza podataka
- **Docker Compose** – orkestracija servisa

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
     
5. React frontend će biti dostupan na:
     ```
     http://localhost

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
    docker compose exec laravel-app composer install
    docker compose exec laravel-app php artisan key:generate
    docker compose exec laravel-app php artisan config:clear
    docker compose exec laravel-app php artisan migrate
    docker compose exec laravel-app php artisan db:seed

- Frontend koristi Axios sa baseURL:
    ```js
    baseURL: 'http://localhost/api'

## 📋 Funkcionalnosti
- Registracija i prijava korisnika (JWT tokeni)
- Pregled kategorija prihoda i rashoda
- Dodavanje i izmena transakcija
- Pregled statistike finansija
- Seed podaci za testiranje (korisnici, kategorije, transakcije)

## 🧪 Testiranje

- Proveri bazu podataka kroz DatabaseClient ekstenziju na Visual Studio Code koristeći:
- Host: localhost
- Port: 3306
- Database: finansije
- User: laravel
- Password: laravel

📖 Ovaj README služi kao vodič za instalaciju i pokretanje projekta, kao i pregled osnovnih funkcionalnosti aplikacije.
