<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Transakcija::factory(20)->create();
        Kataegorija::factory(15)->create();
        Budzet::factory(10)->create();
        Dokument::factory(15)->create();
        FinansijskiCilj::factory(10)->create();
        Podsetnik::factory(5)->create();

       
    }
}
