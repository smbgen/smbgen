<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Tenant;

use function Pest\Laravel\assertDatabaseHas;

it('displays the trial signup form', function () {
    $response = $this->get('/trial');

    $response->assertSuccessful();
    $response->assertSee('Start Your Free Trial');
    $response->assertSee('Company Name');
    $response->assertSee('Your Name');
    $response->assertSee('Email');
});

it('creates a new trial tenant and user successfully', function () {
    $data = [
        'company_name' => 'Test Company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->post('/trial', $data);

    // Should redirect to verification notice
    $response->assertRedirect(route('verification.notice'));
    $response->assertSessionHas('success');

    // Verify tenant was created
    expect(Tenant::where('email', 'john@testcompany.com')->exists())->toBeTrue();

    $tenant = Tenant::where('email', 'john@testcompany.com')->first();
    expect($tenant->name)->toBe('Test Company');
    expect($tenant->plan)->toBe('trial');
    expect($tenant->trial_ends_at)->not->toBeNull();
    expect($tenant->is_active)->toBeTrue();

    // Verify user was created
    assertDatabaseHas('users', [
        'email' => 'john@testcompany.com',
        'name' => 'John Doe',
        'tenant_id' => $tenant->id,
        'role' => 'company_administrator',
    ]);

    // Verify user is logged in
    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->email)->toBe('john@testcompany.com');
});

it('validates required fields', function () {
    $response = $this->post('/trial', []);

    $response->assertSessionHasErrors(['company_name', 'name', 'email', 'password']);
});

it('validates email format', function () {
    $response = $this->post('/trial', [
        'company_name' => 'Test Company',
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
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('creates a unique subdomain for each tenant', function () {
    $data1 = [
        'company_name' => 'Test Company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $data2 = [
        'company_name' => 'Test Company',
        'name' => 'Jane Doe',
        'email' => 'jane@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post('/trial', $data1);
    $tenant1 = Tenant::where('email', 'john@testcompany.com')->first();

    // Logout the first user
    auth()->logout();

    $this->post('/trial', $data2);
    $tenant2 = Tenant::where('email', 'jane@testcompany.com')->first();

    // Subdomains should be different even though company names are the same
    expect($tenant1->subdomain)->not->toBe($tenant2->subdomain);
});

it('creates a domain mapping for the tenant', function () {
    $data = [
        'company_name' => 'Test Company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post('/trial', $data);

    $tenant = Tenant::where('email', 'john@testcompany.com')->first();

    expect($tenant->domains->count())->toBe(1);
    expect($tenant->domains->first()->domain)->toContain($tenant->subdomain);
});

it('sets trial to expire in 14 days', function () {
    $data = [
        'company_name' => 'Test Company',
        'name' => 'John Doe',
        'email' => 'john@testcompany.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post('/trial', $data);

    $tenant = Tenant::where('email', 'john@testcompany.com')->first();

    $expectedDate = now()->addDays(14);
    expect($tenant->trial_ends_at->diffInDays($expectedDate))->toBeLessThan(1);
});

it('displays Register with Google button on trial signup page', function () {
    $response = $this->get('/trial');

    $response->assertSuccessful();
    $response->assertSee('Register with Google');
    $response->assertSee('trial/google/redirect');
});

it('redirects to Google OAuth for trial signup', function () {
    $response = $this->get('/trial/google/redirect');

    // Should redirect to Google OAuth
    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('accounts.google.com');
});

it('creates trial tenant and user from Google OAuth callback', function () {
    // Skip on SQLite due to tenant schema differences
    if (DB::connection()->getDriverName() === 'sqlite') {
        $this->markTestSkipped('Tenant creation tests are skipped on SQLite due to schema differences.');
    }

    // Mock Socialite Google user
    $mockUser = Mockery::mock('Laravel\Socialite\Two\User');
    $mockUser->shouldReceive('getId')->andReturn('google-123');
    $mockUser->shouldReceive('getName')->andReturn('Jane Smith');
    $mockUser->shouldReceive('getEmail')->andReturn('jane@example.com');

    $mockDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $mockDriver->shouldReceive('stateless')->andReturnSelf();
    $mockDriver->shouldReceive('user')->andReturn($mockUser);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn($mockDriver);

    $response = $this->get('/trial/google/callback');

    // Should redirect to admin dashboard
    $response->assertRedirect(route('admin.dashboard'));

    // Verify user was created
    assertDatabaseHas('users', [
        'email' => 'jane@example.com',
        'name' => 'Jane Smith',
        'google_id' => 'google-123',
        'role' => 'company_administrator',
    ]);

    // Verify tenant was created
    $user = User::where('email', 'jane@example.com')->first();
    expect($user->tenant_id)->not->toBeNull();

    $tenant = Tenant::find($user->tenant_id);
    expect($tenant)->not->toBeNull();
    expect($tenant->email)->toBe('jane@example.com');
    expect($tenant->plan)->toBe('trial');
});

it('prevents duplicate trial signup via Google OAuth', function () {
    // Create existing user
    User::create([
        'name' => 'Existing User',
        'email' => 'existing@example.com',
        'role' => 'client',
    ]);

    // Mock Socialite to return same email
    $mockUser = Mockery::mock('Laravel\Socialite\Two\User');
    $mockUser->shouldReceive('getId')->andReturn('google-456');
    $mockUser->shouldReceive('getName')->andReturn('Existing User');
    $mockUser->shouldReceive('getEmail')->andReturn('existing@example.com');

    $mockDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $mockDriver->shouldReceive('stateless')->andReturnSelf();
    $mockDriver->shouldReceive('user')->andReturn($mockUser);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn($mockDriver);

    $response = $this->get('/trial/google/callback');

    // Should redirect back with error
    $response->assertRedirect(route('trial.show'));
    $response->assertSessionHasErrors(['email']);
});
