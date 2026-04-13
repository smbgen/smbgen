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

        // SaaS Product Module demo data — local/staging only
        if (! app()->isProduction()) {
            $this->call([
                \App\Modules\SaasProductModule\Database\Seeders\DataBrokerSeeder::class,
                \App\Modules\SaasProductModule\Database\Seeders\DemoCustomerSeeder::class,
            ]);
        }
    }
}
