<?php

namespace Database\Seeders;

use App\Models\Budzet;
use App\Models\Dokument;
use App\Models\FinansijskiCilj;
use App\Models\Kategorija;
use App\Models\Podsetnik;
use App\Models\Transakcija;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Kategorija::factory(8)->create();
        Transakcija::factory(20)->create();
        Budzet::factory(10)->create();
        Dokument::factory(15)->create();
        FinansijskiCilj::factory(10)->create();
        Podsetnik::factory(5)->create();

    }
}
