<?php

namespace Database\Factories;

use App\Models\AIGeneration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIGenerationFactory extends Factory
{
    protected $model = AIGeneration::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['blog_post', 'landing_page', 'seo_meta', 'product_description']),
            'prompt' => $this->faker->sentence(10),
            'generated_content' => $this->faker->paragraphs(3, true),
            'model' => 'claude-3-5-sonnet',
            'input_tokens' => $this->faker->numberBetween(100, 500),
            'output_tokens' => $this->faker->numberBetween(200, 1000),
            'total_tokens' => $this->faker->numberBetween(300, 1500),
            'status' => 'success',
            'error_message' => null,
        ];
    }
}
