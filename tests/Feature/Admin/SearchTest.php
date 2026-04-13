<?php

use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->withoutVite();
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view global search page', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.search'));

    $response->assertOk();
    $response->assertViewIs('admin.search.index');
    $response->assertSee('Global Search');
});

test('global search returns json results when query is present', function () {
    $client = Client::factory()->create([
        'name' => 'Global Search Client',
        'email' => 'global-search@example.com',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.search', ['q' => 'Global Search', 'type' => 'clients']));

    $response->assertOk();
    $response->assertJsonPath('clients.0.id', $client->id);
    $response->assertJsonPath('clients.0.name', $client->name);
    $response->assertJsonPath('bookings', []);
    $response->assertJsonPath('leads', []);
    $response->assertJsonPath('invoices', []);
    $response->assertJsonPath('users', []);
});

test('guest cannot access global search', function () {
    $response = $this->get(route('admin.search'));

    $response->assertRedirect(route('login'));
});
