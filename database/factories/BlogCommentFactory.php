<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogComment>
 */
class BlogCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isGuest = fake()->boolean(30);

        return [
            'blog_post_id' => BlogPost::factory(),
            'user_id' => $isGuest ? null : User::factory(),
            'parent_id' => null,
            'author_name' => $isGuest ? fake()->name() : null,
            'author_email' => $isGuest ? fake()->safeEmail() : null,
            'content' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'spam', 'rejected']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate comment is approved
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate comment is pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate comment is from a guest
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
        ]);
    }

    /**
     * Indicate comment is from an authenticated user
     */
    public function authenticated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'author_name' => null,
            'author_email' => null,
        ]);
    }
}
