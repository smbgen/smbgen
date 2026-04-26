<?php

use App\Http\Controllers\DemoController;
use App\Models\User;
use Illuminate\Support\Facades\Config;

beforeEach(function (): void {
    Config::set('app.demo_mode', true);
});

it('shows the demo landing page when demo mode is enabled', function (): void {
    $response = $this->get('/demo');

    $response->assertOk();
    $response->assertViewIs('demo.landing');
    $response->assertSee('View Frontend Interfaces');
    $response->assertSee('Open Home Interface');
});

it('logs out an authenticated user when visiting demo landing', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/demo');

    $response->assertOk();
    $this->assertGuest();
});

it('returns 404 for demo landing when demo mode is disabled', function (): void {
    Config::set('app.demo_mode', false);

    $response = $this->get('/demo');

    $response->assertNotFound();
});

it('returns 404 for demo login when demo mode is disabled', function (): void {
    Config::set('app.demo_mode', false);

    $response = $this->post('/demo/login/admin');

    $response->assertNotFound();
});

it('auto-logs in as demo admin and redirects to admin dashboard', function (): void {
    $user = User::factory()->create([
        'email' => DemoController::DEMO_ADMIN_EMAIL,
        'role' => User::ROLE_ADMINISTRATOR,
    ]);

    $response = $this->post('/demo/login/admin');

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('switches from an existing session to the selected demo account', function (): void {
    $existingUser = User::factory()->create();

    $demoUser = User::factory()->create([
        'email' => DemoController::DEMO_ADMIN_EMAIL,
        'role' => User::ROLE_ADMINISTRATOR,
    ]);

    $response = $this->actingAs($existingUser)->post('/demo/login/admin');

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($demoUser);
});

it('auto-logs in as demo client and redirects to dashboard', function (): void {
    $user = User::factory()->create([
        'email' => DemoController::DEMO_CLIENT_EMAIL,
        'role' => User::ROLE_CLIENT,
    ]);

    $response = $this->post('/demo/login/client');

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('redirects to demo landing with error when demo account does not exist', function (): void {
    $response = $this->post('/demo/login/admin');

    $response->assertRedirect(route('demo.landing'));
    $response->assertSessionHasErrors('demo');
});

it('redirects to demo landing for unknown role', function (): void {
    $response = $this->post('/demo/login/supervillain');

    $response->assertRedirect(route('demo.landing'));
});

it('redirects to demo landing on logout when demo mode is enabled', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('demo.landing'));
});

it('redirects to home on logout when demo mode is disabled', function (): void {
    Config::set('app.demo_mode', false);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect('/');
});
