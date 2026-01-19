<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Podsetnik>
 */
class PodsetnikFactory extends Factory
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
            'opis' => fake()->sentence(2),
            'datum_vreme' => fake()->dateTimeBetween('now', '+2 months'),
            'status' => fake()->boolean(80), // 80% sanse da je aktivan status
        ];
    }
}
