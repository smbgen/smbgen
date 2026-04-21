<?php

use App\Models\User;

it('allows company administrator to view create inspection report page', function () {
    config(['business.features.inspection_reports' => true]);
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/inspection-reports/create')->assertStatus(200);
});

it('forbids regular user from viewing create inspection report page', function () {
    config(['business.features.inspection_reports' => true]);
    $user = User::factory()->client()->create();

    $this->actingAs($user)->get('/admin/inspection-reports/create')->assertForbidden();
});
