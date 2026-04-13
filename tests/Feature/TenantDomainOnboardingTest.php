<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;

it('redirects company administrator to domain onboarding when incomplete', function () {
    putenv('TENANCY_ENABLED=true');

    try {
        $tenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Acme LLC',
            'email' => 'owner@acme.test',
            'subdomain' => 'acme',
            'plan' => 'trial',
            'deployment_mode' => 'shared',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'owner@acme.test',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ADMINISTRATOR,
            'email_verified_at' => now(),
            'is_super_admin' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/domain-onboarding');
    } finally {
        putenv('TENANCY_ENABLED=false');
    }
});

it('stores custom domain and pending dns status', function () {
    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Beta Co',
        'email' => 'owner@beta.test',
        'subdomain' => 'beta',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'email_verified_at' => now(),
        'is_super_admin' => false,
    ]);

    $response = $this->actingAs($user)->patch('/admin/domain-onboarding', [
        'action' => 'save_domain',
        'custom_domain' => 'app.beta.example',
    ]);

    $response->assertSessionHasNoErrors();

    $tenant->refresh();

    expect($tenant->custom_domain)->toBe('app.beta.example');
    expect($tenant->getAttribute('custom_domain_status'))->toBe('pending_dns');
    expect($tenant->getAttribute('domain_onboarding_completed_at'))->not->toBeNull();

    $this->assertDatabaseHas('domains', [
        'tenant_id' => $tenant->id,
        'domain' => 'app.beta.example',
    ]);
});

it('marks custom domain as verified', function () {
    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Gamma Co',
        'email' => 'owner@gamma.test',
        'subdomain' => 'gamma',
        'custom_domain' => 'app.gamma.example',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
        'data' => ['custom_domain_status' => 'pending_dns'],
    ]);
    Domain::create([
        'tenant_id' => $tenant->id,
        'domain' => 'app.gamma.example',
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'email_verified_at' => now(),
        'is_super_admin' => false,
    ]);

    $response = $this->actingAs($user)->patch('/admin/domain-onboarding', [
        'action' => 'mark_verified',
    ]);

    $response->assertSessionHasNoErrors();

    $tenant->refresh();

    expect($tenant->getAttribute('custom_domain_status'))->toBe('verified');
    expect($tenant->getAttribute('custom_domain_verified_at'))->not->toBeNull();
});

it('can complete onboarding with platform subdomain only', function () {
    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Delta Co',
        'email' => 'owner@delta.test',
        'subdomain' => 'delta',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'role' => User::ROLE_ADMINISTRATOR,
        'email_verified_at' => now(),
        'is_super_admin' => false,
    ]);

    $response = $this->actingAs($user)->patch('/admin/domain-onboarding', [
        'action' => 'use_subdomain',
    ]);

    $response->assertSessionHasNoErrors();

    $tenant->refresh();

    expect($tenant->getAttribute('custom_domain_status'))->toBe('using_subdomain');
    expect($tenant->getAttribute('domain_onboarding_completed_at'))->not->toBeNull();
});
