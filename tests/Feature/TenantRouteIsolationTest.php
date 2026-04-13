<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Route;

it('blocks tenant admin routes on central domain when tenancy is enabled', function () {
    config()->set('tenancy.central_domains', ['central.test']);
    putenv('TENANCY_ENABLED=true');

    $response = $this->get('https://central.test/admin/dashboard');

    $response->assertNotFound();

    putenv('TENANCY_ENABLED=false');
});

it('allows normal auth flow for admin routes when tenancy is disabled', function () {
    putenv('TENANCY_ENABLED=false');

    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
});

it('forbids authenticated users from accessing a different tenant domain', function () {
    putenv('TENANCY_ENABLED=true');

    try {
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
    } finally {
        putenv('TENANCY_ENABLED=false');
    }
});
