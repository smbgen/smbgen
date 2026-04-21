<?php

namespace Database\Factories;

use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialPost>
 */
class SocialPostFactory extends Factory
{
    protected $model = SocialPost::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'caption' => fake()->paragraph(),
            'status' => SocialPost::STATUS_DRAFT,
            'scheduled_at' => null,
            'published_at' => null,
            'requires_approval' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => SocialPost::STATUS_DRAFT]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => SocialPost::STATUS_SCHEDULED,
            'scheduled_at' => now()->addHour(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => SocialPost::STATUS_PUBLISHED,
            'published_at' => now()->subHour(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => SocialPost::STATUS_FAILED]);
    }

    public function due(): static
    {
        return $this->state(fn () => [
            'status' => SocialPost::STATUS_SCHEDULED,
            'scheduled_at' => now()->subMinute(), // in the past – due now
        ]);
    }

    public function requiresApproval(): static
    {
        return $this->state(fn () => ['requires_approval' => true]);
    }
}
