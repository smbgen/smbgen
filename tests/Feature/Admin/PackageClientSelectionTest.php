<?php

use App\Models\Client;
use App\Models\User;

beforeEach(function (): void {
    $this->withoutVite();

    $this->admin = User::factory()->admin()->create();
});

test('package create client selector shows client email addresses', function (): void {
    $client = Client::factory()->create([
        'name' => 'Acme Studio',
        'email' => 'hello@acme.test',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.packages.create'));

    $response->assertOk();
    $response->assertSee('Acme Studio (hello@acme.test)');
    $response->assertSee('value="'.$client->id.'"', false);
});

test('package index client filter shows client email addresses', function (): void {
    $client = Client::factory()->create([
        'name' => 'Beacon Labs',
        'email' => 'team@beacon.test',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.packages.index'));

    $response->assertOk();
    $response->assertSee('Beacon Labs (team@beacon.test)');
    $response->assertSee('value="'.$client->id.'"', false);
});
