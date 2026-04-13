<?php

namespace Database\Seeders;

use App\Models\SubscriptionTier;

class SubscriptionTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionTier::create([
            'name' => 'SMB Starter',
            'slug' => 'smb-starter',
            'description' => 'Perfect for small businesses getting started',
            'price_cents' => 9700,
            'billing_period' => 'monthly',
            'stripe_price_id' => 'price_starter',
            'is_active' => true,
            'features' => ['booking', 'client_area', 'messaging', 'landing_pages', 'basic_branding'],
            'limits' => [
                'services' => 10,
                'clients' => 50,
                'users' => 3,
                'bookings_per_month' => 500,
                'storage_gb' => 10,
            ],
            'sort_order' => 1,
        ]);

        SubscriptionTier::create([
            'name' => 'SMB Plus',
            'slug' => 'smb-plus',
            'description' => 'Advanced features for growing businesses',
            'price_cents' => 19700,
            'billing_period' => 'monthly',
            'stripe_price_id' => 'price_plus',
            'is_active' => true,
            'features' => ['booking', 'client_area', 'messaging', 'landing_pages', 'basic_branding', 'cms', 'billing', 'api_access', 'custom_domain', 'advanced_reporting', 'priority_support'],
            'limits' => [
                'services' => 50,
                'clients' => 500,
                'users' => 10,
                'bookings_per_month' => 5000,
                'storage_gb' => 100,
                'api_calls_per_month' => 50000,
            ],
            'sort_order' => 2,
        ]);

        SubscriptionTier::create([
            'name' => 'Dedicated',
            'slug' => 'dedicated',
            'description' => 'Custom enterprise solution',
            'price_cents' => 0, // Custom pricing
            'billing_period' => 'monthly',
            'stripe_price_id' => null,
            'is_active' => true,
            'features' => ['all_features_enabled', 'white_label', 'phone_system', 'unlimited_everything'],
            'limits' => [
                'services' => 999, // Unlimited marker
                'clients' => 999,
                'users' => 999,
                'bookings_per_month' => 999,
                'storage_gb' => 999,
                'api_calls_per_month' => 999,
            ],
            'sort_order' => 3,
        ]);
    }
}
