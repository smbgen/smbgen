<?php

use App\Models\ActivityLog;
use App\Models\BusinessSetting;
use App\Models\SubscriptionTier;
use App\Models\Tenant;
use App\Models\User;
use Stancl\Tenancy\Database\Models\Domain;

beforeEach(function () {
    $this->withoutVite();
    $this->superAdmin = User::factory()->superAdmin()->create();
    $this->admin = User::factory()->admin()->create();
});

test('super admin can access the deployment console dashboard', function () {
    $response = $this->actingAs($this->superAdmin)
        ->get(route('super-admin.dashboard'));

    $response->assertOk()
        ->assertSee('Super Admin Console')
        ->assertSee('Deployment Console');
});

test('company administrator cannot access the super admin dashboard', function () {
    $response = $this->actingAs($this->admin)
        ->getJson(route('super-admin.dashboard'));

    $response->assertForbidden();
});

test('super admin can access platform billing overview', function () {
    $tier = SubscriptionTier::create([
        'name' => 'Professional',
        'slug' => 'professional',
        'description' => 'Professional plan',
        'price_cents' => 9900,
        'billing_period' => 'monthly',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Northwind Agency',
        'email' => 'owner@northwind.test',
        'subdomain' => 'northwind',
        'custom_domain' => 'portal.northwind.test',
        'plan' => 'professional',
        'deployment_mode' => 'shared',
        'subscription_tier_id' => $tier->id,
        'stripe_customer_id' => 'cus_platform_123',
        'stripe_subscription_id' => 'sub_platform_123',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->get(route('super-admin.billing.index'));

    $response->assertOk()
        ->assertSee('Platform Billing')
        ->assertSee('Northwind Agency')
        ->assertSee('Subscribed')
        ->assertSee('Tenant admin dashboard');
});

test('company administrator cannot access platform billing overview', function () {
    $response = $this->actingAs($this->admin)
        ->getJson(route('super-admin.billing.index'));

    $response->assertForbidden();
});

test('super admin is redirected to the super admin dashboard after login', function () {
    $response = $this->post('/login', [
        'email' => $this->superAdmin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('super-admin.dashboard', absolute: false));
});

test('super admin can update deployment console settings', function () {
    $response = $this->actingAs($this->superAdmin)
        ->patch(route('super-admin.deployment-console.update'), [
            'deployment_name' => 'SMBGen Control Plane',
            'deployment_domain' => 'smbgen.test',
            'deployment_environment' => 'production',
            'frontend_module' => 'frontend_site',
            'enabled_modules' => ['frontend_site'],
        ]);

    $response->assertRedirect(route('super-admin.deployment-console'));

    expect(BusinessSetting::get('deployment_name'))->toBe('SMBGen Control Plane')
        ->and(BusinessSetting::get('deployment_environment'))->toBe('production')
        ->and(BusinessSetting::get('deployment_frontend_module'))->toBe('frontend_site')
        ->and(BusinessSetting::get('module_frontend_site_enabled'))->toBeTrue();
});

test('deployment console shows logged-in user queue with tenant association sorted latest first', function () {
    $olderTenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Older Tenant',
        'email' => 'owner@older.test',
        'subdomain' => 'older',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $newerTenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Newer Tenant',
        'email' => 'owner@newer.test',
        'subdomain' => 'newer',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $olderUser = User::factory()->create([
        'tenant_id' => $olderTenant->id,
        'name' => 'Older User',
        'email' => 'older-user@test.com',
    ]);

    $newerUser = User::factory()->create([
        'tenant_id' => $newerTenant->id,
        'name' => 'Newer User',
        'email' => 'newer-user@test.com',
    ]);

    $olderLogin = ActivityLog::create([
        'user_id' => $olderUser->id,
        'action' => 'login',
        'description' => 'User logged in',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
    ]);
    $olderLogin->forceFill(['created_at' => now()->subHour()])->save();

    $newerLogin = ActivityLog::create([
        'user_id' => $newerUser->id,
        'action' => 'login_google',
        'description' => 'User logged in via Google OAuth',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
    ]);
    $newerLogin->forceFill(['created_at' => now()])->save();

    $response = $this->actingAs($this->superAdmin)
        ->get(route('super-admin.deployment-console'));

    $response->assertOk()
        ->assertSee('User Management Queue')
        ->assertSee('Older Tenant')
        ->assertSee('Newer Tenant')
        ->assertSeeInOrder(['newer-user@test.com', 'older-user@test.com']);
});

test('frontend site module can be disabled by deployment settings', function () {
    BusinessSetting::set('module_frontend_site_enabled', false, 'boolean');

    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('super admin routes are central-only when tenancy is enabled', function () {
    config()->set('app.url', 'https://central.test');
    config()->set('tenancy.central_domains', ['central.test']);

    putenv('TENANCY_ENABLED=true');

    $response = $this->actingAs($this->superAdmin)
        ->get('https://tenant.test/super-admin');

    $response->assertNotFound();

    putenv('TENANCY_ENABLED=false');
});

test('super admin routes are accessible on configured central domain', function () {
    config()->set('app.url', 'https://central.test');
    config()->set('tenancy.central_domains', ['central.test']);

    putenv('TENANCY_ENABLED=true');

    $response = $this->actingAs($this->superAdmin)
        ->get('https://central.test/super-admin');

    $response->assertOk();

    putenv('TENANCY_ENABLED=false');
});

test('super admin can impersonate a tenant admin and is redirected to the tenant admin dashboard', function () {
    config()->set('app.url', 'https://central.test');

    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Demo Tenant',
        'email' => 'owner@demo.test',
        'subdomain' => 'demo',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    Domain::create([
        'tenant_id' => $tenant->id,
        'domain' => 'demo.central.test',
    ]);

    $tenantAdmin = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->post(route('super-admin.tenants.impersonate', $tenant));

    $response->assertRedirect('https://demo.central.test/admin/dashboard');
    expect(auth()->id())->toBe($tenantAdmin->id);
    expect(session('super_admin_impersonating.super_admin_id'))->toBe($this->superAdmin->id);
});

test('impersonating super admin can stop impersonating from tenant admin surface', function () {
    config()->set('app.url', 'https://central.test');

    $tenantAdmin = User::factory()->create([
        'tenant_id' => 'tenant-123',
        'role' => User::ROLE_ADMINISTRATOR,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($tenantAdmin)
        ->withSession([
            'super_admin_impersonating' => [
                'super_admin_id' => $this->superAdmin->id,
                'tenant_id' => 'tenant-123',
                'tenant_name' => 'Demo Tenant',
            ],
        ])
        ->post(route('admin.stop-impersonating'));

    $response->assertRedirect(route('super-admin.tenants.index', absolute: true));
    expect(auth()->id())->toBe($this->superAdmin->id);
    expect(session()->has('super_admin_impersonating'))->toBeFalse();
});
