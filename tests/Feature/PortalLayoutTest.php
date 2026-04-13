<?php

use App\Models\Tenant;
use App\Models\User;

it('shows verification page inside portal layout for unverified user', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
    $response->assertSee('Verify Your Email');
    $response->assertSee('Resend Verification Email');
    // Should use portal layout (has page-shell class)
    $response->assertSee('page-shell', false);
    // Should NOT use guest layout pattern (no loginParticles canvas)
    $response->assertDontSee('loginParticles');
});

it('does not render loginParticles on verification page', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
    $response->assertDontSee('loginParticles');
});

it('client layout does not have verification blur overlay', function () {
    $user = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $content = $response->getContent();
    expect($content)->not->toContain('Verify Your Account');
});

it('shows combined billing card on client dashboard', function () {
    config(['business.features.billing' => true]);

    $user = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Billing');
    $response->assertSee('View Invoices');
    $response->assertSee('Pay Now');
});

it('shows workspace overview when tenant context is available on dashboard', function () {
    $tenant = Tenant::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Workspace Co',
        'email' => 'owner@workspace.test',
        'subdomain' => 'workspace',
        'custom_domain' => 'portal.workspace.test',
        'plan' => 'trial',
        'deployment_mode' => 'shared',
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'role' => 'client',
        'tenant_id' => $tenant->id,
    ]);

    app()->instance('currentTenant', $tenant);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Workspace Overview');
    $response->assertSee('Services Enabled');
    $response->assertSee('Current Tier');
    $response->assertSee('workspace');
});
