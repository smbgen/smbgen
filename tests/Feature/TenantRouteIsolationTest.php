<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

it('blocks tenant admin routes on central domain when tenancy is enabled', function () {
    config()->set('tenancy.central_domains', ['central.test']);
    config()->set('app.tenancy_enabled', true);

    $response = $this->get('https://central.test/admin/dashboard');

    $response->assertNotFound();
});

it('allows normal auth flow for admin routes when tenancy is disabled', function () {
    putenv('TENANCY_ENABLED=false');

    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
});

it('forbids authenticated users from accessing a different tenant domain', function () {
    config()->set('app.tenancy_enabled', true);

    $tenantA = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Tenant A',
        'email' => 'a@example.com',
        'subdomain' => 'a',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $tenantB = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Tenant B',
        'email' => 'b@example.com',
        'subdomain' => 'b',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    Route::middleware(['web', 'tenantUser'])->get('/_tenant-user-check', fn () => response('ok'));

    $user = User::factory()->create([
        'tenant_id' => $tenantB->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'is_super_admin' => false,
        'email_verified_at' => now(),
    ]);

    app()->instance('currentTenant', $tenantA);

    $response = $this->actingAs($user)->get('/_tenant-user-check');

    $response->assertForbidden();
});

it('redirects tenant administrators from central login to their tenant admin host', function () {
    config()->set('app.url', 'https://central.test');
    config()->set('app.tenancy_enabled', true);

    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Tenant Redirect',
        'email' => 'owner@tenant-redirect.test',
        'subdomain' => 'tenant-redirect',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'email' => 'owner@tenant-redirect.test',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
        'is_super_admin' => false,
    ]);

    $response = $this->post('https://central.test/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('https://tenant-redirect.central.test/admin/domain-onboarding');
});
