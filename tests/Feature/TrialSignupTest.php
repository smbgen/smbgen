<?php

use App\Models\User;

it('displays the trial signup form', function () {
    $response = $this->get('/trial');

    $response->assertSuccessful();
    $response->assertSee('Start Your Free Trial');
    $response->assertSee('What happens next');
    $response->assertSee('Company Name');
    $response->assertSee('Your Name');
    $response->assertSee('Email');
});

it('creates a new trial user successfully', function () {
    $data = [
        'company_name' => 'Test Company',
        'subdomain' => 'test-company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->post('/trial', $data);

    $response->assertRedirect();

    // Verify user was created
    $this->assertDatabaseHas('users', [
        'tenant_id' => User::where('email', 'john@testcompany.com')->value('tenant_id'),
        'email' => 'john@testcompany.com',
        'name' => 'John Doe',
        'role' => 'tenant_admin',
    ]);

    $tenantId = User::where('email', 'john@testcompany.com')->value('tenant_id');
    expect($tenantId)->not->toBeNull();

    $this->assertDatabaseHas('tenants', [
        'id' => $tenantId,
        'subdomain' => 'test-company',
        'plan' => 'trial',
    ]);

    $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
    $this->assertDatabaseHas('domains', [
        'tenant_id' => $tenantId,
        'domain' => 'test-company.'.$baseHost,
    ]);

    $user = User::where('email', 'john@testcompany.com')->first();
    expect($user->trial_ends_at)->not->toBeNull();
    expect($user->trial_ends_at->diffInDays(now()->addDays(14)))->toBeLessThan(1);
});

it('validates required fields', function () {
    $response = $this->post('/trial', []);

    $response->assertSessionHasErrors(['company_name', 'subdomain', 'name', 'email', 'password']);
});

it('validates email format', function () {
    $response = $this->post('/trial', [
        'company_name' => 'Test Company',
        'subdomain' => 'test-company',
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('validates unique email', function () {
    // Create existing user
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $response = $this->post('/trial', [
        'company_name' => 'Test Company',
        'subdomain' => 'test-company',
        'name' => 'John Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('validates password confirmation', function () {
    $response = $this->post('/trial', [
        'company_name' => 'Test Company',
        'subdomain' => 'test-company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('validates password minimum length', function () {
    $response = $this->post('/trial', [
        'company_name' => 'Test Company',
        'subdomain' => 'test-company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('displays one-click with google button on trial signup page', function () {
    $response = $this->get('/trial');

    $response->assertSuccessful();
    $response->assertSee('One-click with Google');
    $response->assertSee('trial/google/redirect');
    $response->assertSee('Workspace Subdomain');
});

it('redirects to Google OAuth for trial signup', function () {
    $response = $this->get('/trial/google/redirect');

    $response->assertRedirect(route('auth.google.redirect'));
});
