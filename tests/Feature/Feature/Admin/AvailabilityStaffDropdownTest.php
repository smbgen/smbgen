<?php

use App\Models\User;

test('availability index shows company_administrator users in staff dropdown', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $response = $this->actingAs($admin)->get(route('admin.availability.index'));

    $response->assertOk();
    $response->assertSee($admin->name);
});

test('availability index shows legacy administrator role users in staff dropdown', function () {
    $primaryAdmin = User::factory()->create(['role' => 'company_administrator']);
    $legacyAdmin = User::factory()->create(['role' => 'administrator']);

    $response = $this->actingAs($primaryAdmin)->get(route('admin.availability.index'));

    $response->assertOk();
    $response->assertSee($legacyAdmin->name);
});

test('availability index shows tenant_admin role users in staff dropdown', function () {
    $primaryAdmin = User::factory()->create(['role' => 'company_administrator']);
    $tenantAdmin = User::factory()->create(['role' => 'tenant_admin']);

    $response = $this->actingAs($primaryAdmin)->get(route('admin.availability.index'));

    $response->assertOk();
    $response->assertSee($tenantAdmin->name);
});

test('availability index does not show client role users in staff dropdown', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $client = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($admin)->get(route('admin.availability.index'));

    $response->assertOk();
    $response->assertDontSee($client->name);
});
