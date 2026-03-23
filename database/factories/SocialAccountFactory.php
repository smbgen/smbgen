<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'platform' => 'linkedin',
            'account_name' => fake()->company(),
            'account_url' => fake()->url(),
            'page_id' => fake()->numerify('########'),
            'page_name' => fake()->company() . ' Page',
            'credentials' => [
                'access_token' => fake()->sha256(),
                'refresh_token' => fake()->sha256(),
            ],
            'access_token_expires_at' => now()->addHour()->toDateTimeString(),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['access_token_expires_at' => now()->subHour()->toDateTimeString()]);
    }
}
