<?php

use App\Models\Client;
use App\Models\LeadForm;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can access dashboard', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertViewIs('admin.dashboard');
});

test('non-admin cannot access dashboard', function () {
    $regularUser = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($regularUser)
        ->get(route('admin.dashboard'));

    $response->assertForbidden();
});

test('guest cannot access dashboard', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
});

test('dashboard displays correct stat cards', function () {
    // Create test data
    Client::factory()->count(3)->create();
    LeadForm::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Clients');
    $response->assertSee('Leads');
});

test('dashboard shows bookings stat when appointments feature is enabled', function () {
    config(['business.features.booking' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Bookings');
})->skip('Booking factory needs to be created');

test('dashboard shows cms pages stat when cms feature is enabled', function () {
    config(['business.features.cms' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('CMS Pages');
});

test('dashboard displays quick actions', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Quick Actions');
    $response->assertSee('New Client');
    $response->assertSee('View Leads');
});

test('dashboard displays recent leads', function () {
    LeadForm::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Recent Leads');
});

test('dashboard displays system tools', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('System Tools');
    $response->assertSee('Email Logs');
    $response->assertSee('Settings');
});

test('dashboard displays quick links', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Quick Links');
    $response->assertSee('All Clients');
    $response->assertSee('All Leads');
});

test('dashboard displays booking manager when appointments enabled', function () {
    config(['business.features.booking' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Booking System');
});

test('dashboard does not display booking manager when appointments disabled', function () {
    config(['business.features.booking' => false]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertDontSee('Booking System');
});
