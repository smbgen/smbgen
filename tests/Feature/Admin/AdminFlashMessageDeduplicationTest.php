<?php

use App\Models\User;

test('admin success flash is rendered once on package index', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->withSession(['success' => 'Package flash test'])
        ->get(route('admin.packages.index'));

    $response->assertOk();

    expect(substr_count($response->getContent(), 'Package flash test'))->toBe(1);
});
