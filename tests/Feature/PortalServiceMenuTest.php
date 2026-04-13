<?php

use App\Models\User;

it('allows an authenticated verified user to update tier and enabled services', function () {
    $user = User::factory()->create([
        'account_tier' => 'free',
        'enabled_services' => ['free_open_source'],
    ]);

    $response = $this->actingAs($user)->patch(route('portal.service-menu.update'), [
        'account_tier' => 'smb_pro',
        'enabled_services' => ['free_open_source', 'consulting', 'smb_pro'],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Portal services and tier updated.');

    expect($user->fresh()->account_tier)->toBe('smb_pro');
    expect($user->fresh()->enabled_services)->toBe(['free_open_source', 'consulting', 'smb_pro']);
});

it('rejects invalid tier values', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('portal.service-menu.update'), [
        'account_tier' => 'enterprise_unlimited',
        'enabled_services' => ['free_open_source'],
    ]);

    $response->assertSessionHasErrors('account_tier');
});

it('requires authentication to update portal service menu', function () {
    $response = $this->patch(route('portal.service-menu.update'), [
        'account_tier' => 'free',
    ]);

    $response->assertRedirect(route('login'));
});
