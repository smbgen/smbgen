<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns autocomplete results without requiring a clients company column', function () {
    $admin = User::factory()->admin()->create([
        'email_verified_at' => now(),
    ]);

    Client::create([
        'name' => 'Alex Client',
        'email' => 'alex.client@example.com',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.email.all-emails', ['q' => 'alex']));

    $response->assertOk();
    $response->assertJsonFragment([
        'email' => 'alex.client@example.com',
        'name' => 'Alex Client',
        'source' => 'client',
    ]);
});
