<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookingDate = fake()->dateTimeBetween('+1 day', '+30 days');
        $bookingTime = fake()->time('H:i:s');

        return [
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'duration' => fake()->randomElement([30, 45, 60]),
            'status' => \App\Models\Booking::STATUS_CONFIRMED,
            'notes' => fake()->optional()->sentence(),
            'property_address' => fake()->optional()->address(),
            'google_calendar_event_id' => null,
            'google_meet_link' => null,
            'staff_id' => null,
        ];
    }

    /**
     * Indicate that the booking has a Google Meet link.
     */
    public function withMeetLink(): static
    {
        return $this->state(fn (array $attributes) => [
            'google_meet_link' => 'https://meet.google.com/'.fake()->regexify('[a-z]{3}-[a-z]{4}-[a-z]{3}'),
            'google_calendar_event_id' => fake()->uuid(),
        ]);
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => \App\Models\Booking::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => \App\Models\Booking::STATUS_CANCELLED,
        ]);
    }
}
