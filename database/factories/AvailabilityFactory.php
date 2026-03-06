<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'duration' => 30,
            'break_period_minutes' => 0,
            'minimum_booking_notice_hours' => 24,
            'maximum_booking_days_ahead' => 30,
            'timezone' => 'America/New_York',
            'is_active' => true,
        ];
    }
}
