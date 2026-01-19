<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Kategorija;
use App\Enums\TipTransakcije;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transakcija>
 */
class TransakcijaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idKorisnik' => User::factory(),
            'idKategorija' => Kategorija::factory(),
            'datum_vreme' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tipTransakcije' => fake()->randomElement([TipTransakcije::PRIHOD, TipTransakcije::RASHOD]),
            'iznos' => fake()->randomFloat(2, 10, 100000),
            'opis' => fake()->sentence(), 
            'valuta' => fake()->randomElement(['USD', 'EUR', 'RSD'])    
        ];
    }
}
