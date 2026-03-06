<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->paragraph(),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(5)).'</p>',
            'content_blocks' => [
                [
                    'type' => 'heading',
                    'content' => fake()->sentence(),
                    'level' => 'h2',
                ],
                [
                    'type' => 'text',
                    'content' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
                ],
                [
                    'type' => 'quote',
                    'content' => fake()->sentence(),
                    'author' => fake()->name(),
                ],
                [
                    'type' => 'text',
                    'content' => '<p>'.implode('</p><p>', fake()->paragraphs(2)).'</p>',
                ],
            ],
            'author_id' => User::factory(),
            'featured_image' => fake()->imageUrl(1200, 630, 'blog', true),
            'status' => 'published',
            'published_at' => now(),
            'seo_title' => fake()->sentence(),
            'seo_description' => fake()->paragraph(),
            'seo_keywords' => implode(', ', fake()->words(5)),
            'view_count' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Create a draft post
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Create a scheduled post
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'published_at' => now()->addDays(fake()->numberBetween(1, 30)),
        ]);
    }

    /**
     * Create an archived post
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => now()->subDays(fake()->numberBetween(30, 365)),
        ]);
    }

    /**
     * Create a post without content blocks
     */
    public function simpleContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_blocks' => null,
        ]);
    }

    /**
     * Create a post with rich content blocks
     */
    public function richContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_blocks' => [
                [
                    'type' => 'heading',
                    'content' => fake()->sentence(),
                    'level' => 'h2',
                ],
                [
                    'type' => 'text',
                    'content' => '<p>'.implode('</p><p>', fake()->paragraphs(2)).'</p>',
                ],
                [
                    'type' => 'image',
                    'url' => fake()->imageUrl(800, 600, 'technology', true),
                    'alt' => fake()->sentence(),
                    'caption' => fake()->sentence(),
                ],
                [
                    'type' => 'heading',
                    'content' => fake()->sentence(),
                    'level' => 'h3',
                ],
                [
                    'type' => 'text',
                    'content' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
                ],
                [
                    'type' => 'code',
                    'content' => '<?php\n\nfunction example() {\n    return "Hello World";\n}',
                    'language' => 'php',
                ],
                [
                    'type' => 'video',
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'platform' => 'youtube',
                ],
                [
                    'type' => 'callout',
                    'content' => fake()->paragraph(),
                    'style' => 'info',
                ],
            ],
        ]);
    }
}
