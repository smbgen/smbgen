<?php

namespace Database\Seeders;

use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionTierSeeder extends Seeder
{
    public function run(): void
    {
        // SMB Starter - $97/month
        SubscriptionTier::updateOrCreate(
            ['slug' => 'smb-starter'],
            [
                'name' => 'SMB Starter',
                'description' => 'Cost-effective client portal with up to 5 pages and 10 GB storage',
                'price_cents' => 9700,
                'billing_period' => 'monthly',
                'stripe_price_id' => null,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    'booking' => true,
                    'client_area' => true,
                    'cms' => true,
                    'landing_pages' => true,
                    'custom_domain' => true,
                    'priority_support' => false,
                    'messaging' => false,
                    'api_access' => false,
                    'white_label' => false,
                ],
                'limits' => [
                    'cms_pages' => 5,
                    'storage_gb' => 10,
                    'max_users' => 3,
                    'max_clients' => 250,
                    'max_bookings_per_month' => 250,
                    'file_size_limit_mb' => 25,
                ],
            ]
        );

        // SMB Plus - $197/month
        SubscriptionTier::updateOrCreate(
            ['slug' => 'smb-plus'],
            [
                'name' => 'SMB Plus',
                'description' => 'Premium support, unlimited pages, 50 GB storage, OAuth, custom booking URL',
                'price_cents' => 19700,
                'billing_period' => 'monthly',
                'stripe_price_id' => null,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    'booking' => true,
                    'client_area' => true,
                    'cms' => true,
                    'landing_pages' => true,
                    'custom_domain' => true,
                    'priority_support' => true,
                    'oauth' => true,
                    'custom_booking_url' => true,
                    'messaging' => false,
                    'api_access' => false,
                    'white_label' => false,
                ],
                'limits' => [
                    'cms_pages' => 999,
                    'storage_gb' => 50,
                    'max_users' => 10,
                    'max_clients' => 1000,
                    'max_bookings_per_month' => 500,
                    'file_size_limit_mb' => 100,
                ],
            ]
        );

        // Dedicated - Custom pricing with AI and Pay integration
        SubscriptionTier::updateOrCreate(
            ['slug' => 'dedicated'],
            [
                'name' => 'Dedicated (Custom)',
                'description' => 'Dedicated instance with AI integrations, pay integration, and full white-label',
                'price_cents' => 0,
                'billing_period' => 'custom',
                'stripe_price_id' => null,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    'booking' => true,
                    'client_area' => true,
                    'cms' => true,
                    'landing_pages' => true,
                    'custom_domain' => true,
                    'priority_support' => true,
                    'messaging' => true,
                    'api_access' => true,
                    'white_label' => true,
                    'phone_system' => true,
                    'ai_integrations' => true,
                    'pay_integration' => true,
                ],
                'limits' => [
                    'cms_pages' => 999,
                    'storage_gb' => 1000,
                    'max_users' => 999,
                    'max_clients' => 10000,
                    'max_bookings_per_month' => 999999,
                    'file_size_limit_mb' => 500,
                    'api_calls_per_month' => 999999,
                ],
            ]
        );
    }
}
