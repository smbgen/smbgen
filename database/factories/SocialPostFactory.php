<?php

namespace Database\Factories;

use App\Enums\SocialPlatform;
use App\Enums\SocialPostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialPost>
 */
class SocialPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'platform' => fake()->randomElement(SocialPlatform::cases()),
            'content' => fake()->paragraph(),
            'status' => SocialPostStatus::Draft,
            'ai_generated' => fake()->boolean(70),
            'scheduled_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'published_at' => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => SocialPostStatus::Scheduled,
            'scheduled_at' => fake()->dateTimeBetween('now', '+7 days'),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => SocialPostStatus::Published,
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
