<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            LeadFormSeeder::class,
            MessageSeeder::class,
        ]);

        // Extreme demo data — local/staging only
        if (! app()->isProduction()) {
            $optionalSeeders = array_filter([
                \App\Modules\CleanSlate\Database\Seeders\DataBrokerSeeder::class,
                \App\Modules\CleanSlate\Database\Seeders\DemoCustomerSeeder::class,
            ], static fn (string $seeder): bool => class_exists($seeder));

            if ($optionalSeeders !== []) {
                $this->call($optionalSeeders);
            }
        }
    }
}
