<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo client record that matches the demo user
        Client::firstOrCreate([
            'email' => 'demo@example.com',
        ], [
            'name' => 'Demo Client Company',
            'phone' => '(555) 123-4567',
            'notes' => 'Demo client for testing purposes',
            'source_site' => 'demo',
        ]);

        // Create additional random clients for testing
        Client::factory()->count(1)->create();
    }
}
