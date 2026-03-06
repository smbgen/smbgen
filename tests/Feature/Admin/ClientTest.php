<?php

use App\Models\Client;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view clients index', function () {
    Client::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('clients.index'));

    $response->assertOk();
    $response->assertViewIs('admin.clients.index');
    $response->assertViewHas('clients');
});

test('admin can view single client', function () {
    $client = Client::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('clients.show', $client));

    $response->assertOk();
    $response->assertSee('John Doe');
    $response->assertSee('john@example.com');
});

test('admin can create new client', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('clients.store'), [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '555-1234',
            'company' => 'Acme Corp',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('clients', [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);
});

test('admin can update client', function () {
    $client = Client::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('clients.update', $client), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '555-5678',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
});

test('admin can delete client', function () {
    $client = Client::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete(route('clients.destroy', $client));

    $response->assertRedirect();

    $this->assertDatabaseMissing('clients', [
        'id' => $client->id,
    ]);
});

test('client email must be unique', function () {
    Client::factory()->create(['email' => 'duplicate@example.com']);

    $response = $this->actingAs($this->admin)
        ->post(route('clients.store'), [
            'name' => 'Another Client',
            'email' => 'duplicate@example.com',
        ]);

    $response->assertSessionHasErrors('email');
});

test('client name is required', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('clients.store'), [
            'email' => 'test@example.com',
        ]);

    $response->assertSessionHasErrors('name');
});

test('client email is required', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('clients.store'), [
            'name' => 'Test Client',
        ]);

    $response->assertSessionHasErrors('email');
});

test('non-admin cannot create clients', function () {
    $regularUser = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($regularUser)
        ->post(route('clients.store'), [
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

    $response->assertForbidden();
});

test('non-admin cannot update clients', function () {
    $regularUser = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create();

    $response = $this->actingAs($regularUser)
        ->put(route('clients.update', $client), [
            'name' => 'Updated',
            'email' => 'updated@example.com',
        ]);

    $response->assertForbidden();
});

test('non-admin cannot delete clients', function () {
    $regularUser = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create();

    $response = $this->actingAs($regularUser)
        ->delete(route('clients.destroy', $client));

    $response->assertForbidden();
});
