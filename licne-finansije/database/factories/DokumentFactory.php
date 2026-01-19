<?php

namespace Database\Factories;

use App\Models\Transakcija;
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
            'transakcija_id' => Transakcija::factory(),
            'naziv' => fake()->word() . '.pdf',
            'datum' => fake()->dateTime(),
            'putanja' => '/documents/' . fake()->word() . '.pdf',
            'tip' => fake()->randomElement(['pdf', 'docx', 'txt', 'xlsx', 'jpg'])
        ];
    }
}
