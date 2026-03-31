<?php

namespace Database\Factories;

use App\Enums\TipTransakcije;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategorija>
 */
class KategorijaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tip = fake()->randomElement(TipTransakcije::cases());

        $prihod = ['Plata', 'Poklon', 'Bonus', 'Stipendija', 'Ostalo'];
        $rashod = ['Hrana', 'Stanovanje', 'Računi', 'Prevoz', 'Zdravstvo', 'Obrazovanje', 'Zabava', 'Odeća', 'Ostalo'];

        return [
            'idKorisnik'=> User::factory(),
            'naziv' => fake()->randomElement($tip === 'prihod' ? $prihod : $rashod),
            'opis' => fake()->sentence(),
        ];
    }
}
