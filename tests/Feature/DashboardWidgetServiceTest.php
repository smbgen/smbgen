<?php

use App\Models\User;
use App\Services\DashboardWidgetService;

it('detects google calendar connection when user has refresh token', function () {
    config(['business.features.booking' => true]);

    // Create user with Google refresh token
    User::factory()->create([
        'google_refresh_token' => 'test_refresh_token',
        'google_calendar_id' => 'test_calendar_id',
    ]);

    $service = app(DashboardWidgetService::class);
    $data = $service->getBookingManagerData();

    expect($data['enabled'])->toBeTrue()
        ->and($data['googleConnected'])->toBeTrue();
});

it('detects no google calendar connection when no users have refresh tokens', function () {
    config(['business.features.booking' => true]);

    // Create user without Google refresh token
    User::factory()->create([
        'google_refresh_token' => null,
        'google_calendar_id' => null,
    ]);

    $service = app(DashboardWidgetService::class);
    $data = $service->getBookingManagerData();

    expect($data['enabled'])->toBeTrue()
        ->and($data['googleConnected'])->toBeFalse();
});

it('returns disabled when appointments feature is disabled', function () {
    config(['business.features.booking' => false]);

    $service = app(DashboardWidgetService::class);
    $data = $service->getBookingManagerData();

    expect($data['enabled'])->toBeFalse();
});

it('detects google calendar in system health', function () {
    config(['business.features.booking' => true]);

    // Create user with Google refresh token
    User::factory()->create([
        'google_refresh_token' => 'test_refresh_token',
    ]);

    $service = app(DashboardWidgetService::class);
    $health = $service->getSystemHealth();

    $googleHealth = collect($health)->firstWhere('label', 'Google Calendar');

    expect($googleHealth)->not->toBeNull()
        ->and($googleHealth['status'])->toBe('connected');
});
