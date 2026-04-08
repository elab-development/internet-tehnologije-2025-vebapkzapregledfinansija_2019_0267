<?php

namespace App\Enums;

enum Uloga: string
{
    case KORISNIK = 'korisnik';
    case PREMIUM = 'premium';
    case ADMIN = 'admin';
    // admina cemo kreirati sistemski
}
