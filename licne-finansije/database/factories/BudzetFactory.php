<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budzet>
 */
class BudzetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $limit = $this->faker->randomFloat(2, 0, 1000000);

        return [
            'idKorisnik' => User::factory(),
            'mesec' => fake()->numberBetween(1, 12),
            'godina' => fake()->year(),
            'limit' => $limit,
            'potroseno' => fake()->randomFloat(2, 0, $limit),
        ];
    }
}
