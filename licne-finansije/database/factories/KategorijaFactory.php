<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        $tip = fake()->randomElement(['prihod', 'rashod']);

        $prihod = ['Plata', 'Poklon', 'Bonus', 'Stipendija', 'Ostalo'];
        $rashod = ['Hrana', 'Stanovanje', 'Računi', 'Prevoz', 'Zdravstvo', 'Obrazovanje', 'Zabava', 'Odeća', 'Ostalo'];
        return [
            'idKategorija' => null,
            'naziv' => fake()->randomElement($tip === 'prihod' ? $prihod : $rashod),
            'opis' => fake()->sentence(),
        ];
    }
}
