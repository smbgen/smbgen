<?php

use App\Models\ManagedSite;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view cast index', function () {
    ManagedSite::factory()->count(2)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.cast.index'))
        ->assertOk()
        ->assertViewIs('admin.cast.index')
        ->assertViewHas('sites');
});

test('guest cannot access cast index', function () {
    $this->get(route('admin.cast.index'))
        ->assertRedirect(route('login'));
});

test('admin can add a managed site', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cast.store'), [
            'name' => 'Acme Corp',
            'domain' => 'acme.com',
            'status' => 'building',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('managed_sites', ['name' => 'Acme Corp', 'domain' => 'acme.com']);
});

test('adding site validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cast.store'), [])
        ->assertSessionHasErrors(['name', 'status']);
});

test('admin can remove a site', function () {
    $site = ManagedSite::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.cast.destroy', $site))
        ->assertRedirect();

    $this->assertModelMissing($site);
});
