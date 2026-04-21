<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostTarget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialPostTarget>
 */
class SocialPostTargetFactory extends Factory
{
    protected $model = SocialPostTarget::class;

    public function definition(): array
    {
        return [
            'social_post_id' => SocialPost::factory(),
            'social_account_id' => SocialAccount::factory(),
            'status' => SocialPostTarget::STATUS_PENDING,
            'attempt_count' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => SocialPostTarget::STATUS_PUBLISHED,
            'platform_post_id' => (string) fake()->numerify('##########'),
            'published_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => SocialPostTarget::STATUS_FAILED,
            'last_error' => fake()->sentence(),
            'attempt_count' => 1,
        ]);
    }
}
