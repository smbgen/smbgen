<?php

use App\Models\User;

beforeEach(function () {
    $this->withoutVite();

    $this->tenantAdmin = User::factory()->create([
        'role' => User::ROLE_ADMINISTRATOR,
        'tenant_id' => 'tenant-a',
        'email_verified_at' => now(),
    ]);

    $this->sameTenantUser = User::factory()->create([
        'role' => User::ROLE_CLIENT,
        'tenant_id' => 'tenant-a',
    ]);

    $this->otherTenantUser = User::factory()->create([
        'role' => User::ROLE_CLIENT,
        'tenant_id' => 'tenant-b',
    ]);
});

it('shows only users from the administrators tenant', function () {
    $response = $this->actingAs($this->tenantAdmin)
        ->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee($this->sameTenantUser->email);
    $response->assertDontSee($this->otherTenantUser->email);
});

it('forbids editing users from another tenant', function () {
    $response = $this->actingAs($this->tenantAdmin)
        ->get(route('admin.users.edit', $this->otherTenantUser));

    $response->assertForbidden();
});

it('forbids elevating users from another tenant', function () {
    $response = $this->actingAs($this->tenantAdmin)
        ->post(route('admin.users.elevate', $this->otherTenantUser));

    $response->assertForbidden();
});

it('assigns tenant id to newly created users', function () {
    $response = $this->actingAs($this->tenantAdmin)
        ->post(route('admin.users.store'), [
            'name' => 'Tenant Scoped User',
            'email' => 'tenant-scoped@example.test',
            'password' => 'password123',
            'role' => User::ROLE_USER,
        ]);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'tenant-scoped@example.test',
        'tenant_id' => 'tenant-a',
    ]);
});

it('elevate assigns company administrator role and clears super admin flag', function () {
    $targetUser = User::factory()->create([
        'role' => User::ROLE_USER,
        'tenant_id' => 'tenant-a',
        'is_super_admin' => true,
    ]);

    $response = $this->actingAs($this->tenantAdmin)
        ->post(route('admin.users.elevate', $targetUser));

    $response->assertRedirect();

    $targetUser->refresh();
    expect($targetUser->role)->toBe(User::ROLE_ADMINISTRATOR)
        ->and($targetUser->is_super_admin)->toBeFalse();
});
