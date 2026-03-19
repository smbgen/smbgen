<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailSequenceEnrollment>
 */
class EmailSequenceEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail(),
            'contact_name' => fake()->name(),
            'current_step' => 0,
            'status' => 'active',
            'started_at' => now(),
        ];
    }
}
