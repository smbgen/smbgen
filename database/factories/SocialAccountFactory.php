<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    protected $model = SocialAccount::class;

    public function definition(): array
    {
        $platform = fake()->randomElement([
            SocialAccount::PLATFORM_FACEBOOK,
            SocialAccount::PLATFORM_INSTAGRAM,
            SocialAccount::PLATFORM_LINKEDIN,
        ]);

        return [
            'user_id' => User::factory(),
            'platform' => $platform,
            'account_name' => fake()->company(),
            'account_url' => fake()->url(),
            'active' => true,
            'connection_status' => SocialAccount::STATUS_CONNECTED,
            'platform_user_id' => (string) fake()->numerify('##########'),
            'platform_page_id' => $platform !== SocialAccount::PLATFORM_LINKEDIN ? (string) fake()->numerify('##########') : null,
            'platform_page_name' => fake()->company(),
            'access_token' => fake()->sha256(),
            'token_expires_at' => now()->addDays(60),
        ];
    }

    public function facebook(): static
    {
        return $this->state(fn () => ['platform' => SocialAccount::PLATFORM_FACEBOOK]);
    }

    public function instagram(): static
    {
        return $this->state(fn () => ['platform' => SocialAccount::PLATFORM_INSTAGRAM]);
    }

    public function linkedin(): static
    {
        return $this->state(fn () => ['platform' => SocialAccount::PLATFORM_LINKEDIN]);
    }

    public function disconnected(): static
    {
        return $this->state(fn () => [
            'active' => false,
            'connection_status' => SocialAccount::STATUS_REVOKED,
        ]);
    }
}
