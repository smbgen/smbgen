<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailSequenceStep>
 */
class EmailSequenceStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'position' => 1,
            'subject' => fake()->sentence(),
            'body' => fake()->paragraphs(2, true),
            'delay_days' => fake()->numberBetween(0, 14),
        ];
    }
}
