<?php

use App\Models\Tenant;
use App\Models\User;

it('super admin can set a tenant primary domain', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Acme Corp',
        'email' => 'ops@acme.test',
        'subdomain' => 'acme',
        'plan' => 'starter',
        'is_active' => true,
    ]);

    $domain = $tenant->domains()->create([
        'domain' => 'acme.example.com',
    ]);

    $response = $this->actingAs($superAdmin)
        ->post(route('super-admin.tenants.domains.primary', [$tenant, $domain]));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Primary domain updated.');

    expect($tenant->fresh()->custom_domain)->toBe('acme.example.com');
});

it('forbids setting a primary domain that belongs to another tenant', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $tenantA = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Tenant A',
        'email' => 'a@example.test',
        'subdomain' => 'tenant-a',
        'plan' => 'starter',
        'is_active' => true,
    ]);

    $tenantB = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Tenant B',
        'email' => 'b@example.test',
        'subdomain' => 'tenant-b',
        'plan' => 'starter',
        'is_active' => true,
    ]);

    $tenantBDomain = $tenantB->domains()->create([
        'domain' => 'tenant-b.example.com',
    ]);

    $response = $this->actingAs($superAdmin)
        ->post(route('super-admin.tenants.domains.primary', [$tenantA, $tenantBDomain]));

    $response->assertForbidden();
});
