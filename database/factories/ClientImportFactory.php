<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientImport>
 */
class ClientImportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'filename' => fake()->word().'.csv',
            'status' => 'pending',
            'total_rows' => fake()->numberBetween(5, 50),
            'successful_imports' => 0,
            'failed_imports' => 0,
            'errors' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $total = $attributes['total_rows'];
            $successful = fake()->numberBetween((int) ($total * 0.8), $total);

            return [
                'status' => 'completed',
                'successful_imports' => $successful,
                'failed_imports' => $total - $successful,
            ];
        });
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'errors' => ['general' => ['Import failed due to system error']],
        ]);
    }
}
