<?php

use App\Models\User;

it('allows company administrator to view create inspection report page', function () {
    $admin = User::factory()->admin()->create();

    // Ensure migrations are executed for test DB
    $this->artisan('migrate');

    $this->actingAs($admin)->get('/admin/inspection-reports/create')->assertStatus(200);
});

it('forbids regular user from viewing create inspection report page', function () {
    $user = User::factory()->client()->create();

    // Ensure migrations are executed for test DB
    $this->artisan('migrate');

    $this->actingAs($user)->get('/admin/inspection-reports/create')->assertForbidden();
});
