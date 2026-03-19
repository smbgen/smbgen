<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'value' => fake()->randomFloat(2, 500, 25000),
            'stage' => 'new',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function won(): static
    {
        return $this->state(fn () => [
            'stage' => 'closed_won',
            'closed_at' => now(),
        ]);
    }
}
