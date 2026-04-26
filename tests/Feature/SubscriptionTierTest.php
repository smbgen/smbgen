<?php

namespace Tests\Feature;

use App\Models\SubscriptionTier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Skip all tests since this system is now single-tenant
        $this->markTestSkipped('Subscription tier tests are for the old multi-tenant system which has been removed.');
        // $this->seed(\Database\Seeders\SubscriptionTierSeeder::class);
    }

    public function test_subscription_tiers_exist(): void
    {
        $this->assertDatabaseCount('subscription_tiers', 3);

        $tiers = SubscriptionTier::active()->ordered()->get();
        $this->assertEquals('smb-starter', $tiers[0]->slug);
        $this->assertEquals('smb-plus', $tiers[1]->slug);
        $this->assertEquals('dedicated', $tiers[2]->slug);
    }

    public function test_tier_pricing_is_correct(): void
    {
        $starterTier = SubscriptionTier::where('slug', 'smb-starter')->first();
        $plusTier = SubscriptionTier::where('slug', 'smb-plus')->first();

        $this->assertEquals(9700, $starterTier->price_cents);
        $this->assertEquals(19700, $plusTier->price_cents);
    }

    public function test_tier_formatted_price(): void
    {
        $starterTier = SubscriptionTier::where('slug', 'smb-starter')->first();
        $this->assertEquals('$97.00', $starterTier->formattedPrice());
    }

    public function test_tenant_can_have_subscription_tier(): void
    {
        $tier = SubscriptionTier::where('slug', 'smb-plus')->first();

        $tenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Test Tenant',
            'email' => 'test@example.com',
            'subdomain' => 'test',
            'subscription_tier_id' => $tier->id,
        ]);

        $this->assertEquals($tier->id, $tenant->subscription_tier_id);
        $this->assertTrue($tenant->isOnTier('smb-plus'));
    }

    public function test_tenant_has_features_from_tier(): void
    {
        $starterTier = SubscriptionTier::where('slug', 'smb-starter')->first();
        $plusTier = SubscriptionTier::where('slug', 'smb-plus')->first();

        $starterTenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Starter Tenant',
            'email' => 'starter@example.com',
            'subdomain' => 'starter',
            'subscription_tier_id' => $starterTier->id,
            'is_active' => true,
        ]);

        $proTenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Pro Tenant',
            'email' => 'pro@example.com',
            'subdomain' => 'pro',
            'subscription_tier_id' => $plusTier->id,
            'is_active' => true,
        ]);

        // Refresh to ensure relationships are loaded
        $starterTenant = $starterTenant->fresh();
        $proTenant = $proTenant->fresh();

        // Starter has CMS but no priority support
        $this->assertTrue($starterTenant->hasFeature('cms'));
        $this->assertFalse($starterTenant->hasFeature('priority_support'));
        // Plus has CMS and priority support
        $this->assertTrue($proTenant->hasFeature('cms'));
        $this->assertTrue($proTenant->hasFeature('priority_support'));
    }

    public function test_tenant_has_limits_from_tier(): void
    {
        $starterTier = SubscriptionTier::where('slug', 'smb-starter')->first();
        $tenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Test Tenant',
            'email' => 'test@example.com',
            'subdomain' => 'test',
            'subscription_tier_id' => $starterTier->id,
        ]);

        $this->assertEquals(5, $tenant->getLimit('cms_pages'));
        $this->assertEquals(10, $tenant->getLimit('storage_gb'));
    }

    public function test_tenant_within_limit(): void
    {
        $starterTier = SubscriptionTier::where('slug', 'smb-starter')->first();
        $tenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Test Tenant',
            'email' => 'test@example.com',
            'subdomain' => 'test',
            'subscription_tier_id' => $starterTier->id,
        ]);

        // Max users is 3, so 2 users should be within limit
        $this->assertTrue($tenant->isWithinLimit('max_users', 2));
        // But 3 users should not be within limit (current usage must be < limit)
        $this->assertFalse($tenant->isWithinLimit('max_users', 3));
    }

    public function test_super_admin_can_change_tenant_tier(): void
    {
        config()->set('app.super_admin_routes_enabled', true);

        $admin = User::create([
            'tenant_id' => null,
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_super_admin' => true,
            'role' => 'super_admin',
        ]);

        $tenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Test Tenant',
            'email' => 'test@example.com',
            'subdomain' => 'test',
        ]);

        $newTier = SubscriptionTier::where('slug', 'smb-plus')->first();

        $response = $this->actingAs($admin)->post(route('super-admin.tenants.change-tier', $tenant), [
            'subscription_tier_id' => $newTier->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue($tenant->refresh()->isOnTier('smb-plus'));
    }
}
