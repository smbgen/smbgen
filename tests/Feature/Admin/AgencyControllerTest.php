<?php

use App\Models\AgencyPortal;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view agency index', function () {
    AgencyPortal::factory()->count(2)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.agency.index'))
        ->assertOk()
        ->assertViewIs('admin.agency.index')
        ->assertViewHas('portals');
});

test('guest cannot access agency index', function () {
    $this->get(route('admin.agency.index'))
        ->assertRedirect(route('login'));
});

test('admin can create an agency portal', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.agency.store'), [
            'name' => 'L7 Labs Portal',
            'max_client_sites' => 10,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('agency_portals', [
        'name' => 'L7 Labs Portal',
        'slug' => 'l7-labs-portal',
    ]);
});

test('creating portal validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.agency.store'), [])
        ->assertSessionHasErrors(['name', 'max_client_sites']);
});

test('admin can delete a portal', function () {
    $portal = AgencyPortal::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.agency.destroy', $portal))
        ->assertRedirect();

    $this->assertModelMissing($portal);
});
