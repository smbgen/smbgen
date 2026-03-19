<?php

use App\Models\Tenant;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'company_administrator']);
});

test('admin can view tenants index', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.tenants.index'))
        ->assertOk()
        ->assertViewIs('admin.tenants.index')
        ->assertViewHas('tenants');
});

test('guest cannot access tenants index', function () {
    $this->get(route('admin.tenants.index'))
        ->assertRedirect(route('login'));
});

test('non-admin cannot access tenants index', function () {
    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user)
        ->get(route('admin.tenants.index'))
        ->assertForbidden();
});

test('admin can create a tenant', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.tenants.store'), [
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'plan' => 'growth',
            'owner_email' => 'owner@acme.com',
        ])
        ->assertRedirect(route('admin.tenants.index'));

    $this->assertDatabaseHas('tenants', [
        'id' => 'acme',
        'name' => 'Acme Corp',
        'plan' => 'growth',
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('domains', ['tenant_id' => 'acme']);
});

test('creating a tenant auto-assigns plan modules', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.tenants.store'), [
            'name' => 'Scale Co',
            'slug' => 'scaleco',
            'plan' => 'scale',
            'owner_email' => 'owner@scale.com',
        ]);

    $tenant = Tenant::find('scaleco');
    expect($tenant->modules_enabled)->toContain('signal')
        ->and($tenant->modules_enabled)->toContain('surge')
        ->and($tenant->modules_enabled)->not->toContain('extreme');
});

test('creating a tenant validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.tenants.store'), [])
        ->assertSessionHasErrors(['name', 'slug', 'plan', 'owner_email']);
});

test('slug must be unique', function () {
    Tenant::create(['id' => 'taken', 'slug' => 'taken', 'name' => 'Taken', 'plan' => 'starter', 'owner_email' => 'a@b.com']);

    $this->actingAs($this->admin)
        ->post(route('admin.tenants.store'), [
            'name' => 'Other',
            'slug' => 'taken',
            'plan' => 'starter',
            'owner_email' => 'c@d.com',
        ])
        ->assertSessionHasErrors(['slug']);
});

test('admin can delete a tenant', function () {
    $tenant = Tenant::create([
        'id' => 'delete-me',
        'slug' => 'delete-me',
        'name' => 'Delete Me',
        'plan' => 'starter',
        'owner_email' => 'x@y.com',
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.tenants.destroy', $tenant->id))
        ->assertRedirect(route('admin.tenants.index'));

    $this->assertDatabaseMissing('tenants', ['id' => 'delete-me']);
});
