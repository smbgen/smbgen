<?php

use App\Models\Availability;
use App\Models\User;

test('admin can create availability with break period', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $response = $this->actingAs($admin)->post(route('admin.availability.store'), [
        'user_id' => $admin->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '17:00',
        'duration' => 45,
        'break_period_minutes' => 15,
        'minimum_booking_notice_hours' => 24,
        'maximum_booking_days_ahead' => 30,
        'timezone' => 'America/New_York',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('availabilities', [
        'user_id' => $admin->id,
        'duration' => 45,
        'break_period_minutes' => 15,
    ]);
});

test('admin can update availability break period', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);
    $availability = Availability::factory()->create([
        'user_id' => $admin->id,
        'duration' => 30,
        'break_period_minutes' => 0,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.availability.update', $availability), [
        'day_of_week' => $availability->day_of_week,
        'start_time' => $availability->start_time,
        'end_time' => $availability->end_time,
        'duration' => 45,
        'break_period_minutes' => 15,
        'minimum_booking_notice_hours' => $availability->minimum_booking_notice_hours,
        'maximum_booking_days_ahead' => $availability->maximum_booking_days_ahead,
        'timezone' => $availability->timezone,
        'is_active' => $availability->is_active,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('availabilities', [
        'id' => $availability->id,
        'duration' => 45,
        'break_period_minutes' => 15,
    ]);
});

test('break period cannot exceed 120 minutes', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $response = $this->actingAs($admin)->post(route('admin.availability.store'), [
        'user_id' => $admin->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '17:00',
        'duration' => 45,
        'break_period_minutes' => 150,
        'minimum_booking_notice_hours' => 24,
        'maximum_booking_days_ahead' => 30,
        'timezone' => 'America/New_York',
        'is_active' => true,
    ]);

    $response->assertSessionHasErrors('break_period_minutes');
});

test('break period can be zero', function () {
    $admin = User::factory()->create(['role' => 'company_administrator']);

    $response = $this->actingAs($admin)->post(route('admin.availability.store'), [
        'user_id' => $admin->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '17:00',
        'duration' => 45,
        'break_period_minutes' => 0,
        'minimum_booking_notice_hours' => 24,
        'maximum_booking_days_ahead' => 30,
        'timezone' => 'America/New_York',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('availabilities', [
        'user_id' => $admin->id,
        'break_period_minutes' => 0,
    ]);
});
