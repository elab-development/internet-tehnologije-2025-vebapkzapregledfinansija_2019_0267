<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinansijskiCilj>
 */
class FinansijskiCiljFactory extends Factory
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
            'naziv' => fake()->word(),
            'ciljni_iznos' => fake()->randomFloat(2, 100, 100000),
            'trenutni_iznos' => fake()->randomFloat(2, 0, 50000),
            'rok' => fake()->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
        ];
    }
}
