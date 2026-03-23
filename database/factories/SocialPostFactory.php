<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialPost>
 */
class SocialPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'social_account_id' => SocialAccount::factory(),
            'user_id' => User::factory(),
            'title' => fake()->optional()->sentence(),
            'content' => fake()->paragraph(),
            'media_paths' => null,
            'status' => 'draft',
            'scheduled_at' => null,
            'published_at' => null,
            'linkedin_post_id' => null,
            'error_message' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'scheduled_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => 'scheduled',
            'scheduled_at' => now()->addHours(fake()->numberBetween(1, 48)),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now()->subHours(fake()->numberBetween(1, 72)),
            'linkedin_post_id' => 'urn:li:ugcPost:' . fake()->numerify('##########'),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'error_message' => 'API error: ' . fake()->sentence(),
        ]);
    }
}
