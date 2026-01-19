<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dokument>
 */
class DokumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idTransakcija' => Transakcija::factory(),
            'nazivFajla' => fake()->word() . '.pdf',
            'putanjaFajla' => '/documents/' . fake()->word() . '.pdf',
            'velicinaFajla' => fake()->numberBetween(1000, 5000000), // veličina u bajtovima
        ];
    }
}
