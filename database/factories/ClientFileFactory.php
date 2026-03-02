<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientFile>
 */
class ClientFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $client = Client::factory()->create();
        $filename = Str::random(10).'.pdf';
        $path = 'client_files/'.$client->id.'/'.$filename;

        return [
            'client_id' => $client->id,
            'filename' => $filename,
            'original_name' => $this->faker->word().'.pdf',
            'path' => $path,
            'uploaded_by' => $this->faker->randomElement(['client', 'admin']),
        ];
    }
}
