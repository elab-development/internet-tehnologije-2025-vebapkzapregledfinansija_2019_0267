<?php

namespace Database\Factories;

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
            //'naziv' => fake()->word(),
            'ciljaniIznos' => fake()->randomFloat(2, 100, 100000),
            'trenutniIznos' => fake()->randomFloat(2, 0, 50000),
            'rok' => fake()->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
        ];
    }
}
