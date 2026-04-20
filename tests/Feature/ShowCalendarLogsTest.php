<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('shows filtered calendar logs from storage', function () {
    // Prepare a small log file with mixed entries
    $logPath = storage_path('logs/laravel.log');
    File::ensureDirectoryExists(dirname($logPath));

    $entries = [
        '[2026-01-01 00:00:00] local.INFO: Startup complete',
        '[2026-01-02 00:00:00] local.INFO: [GoogleCalendar] createEventForUser called',
        'Stack trace line 1',
        'Stack trace line 2',
        '[2026-01-02 00:05:00] local.ERROR: [Booking] Failed to create event',
        'Details about the failure',
        '[2026-01-03 00:00:00] local.INFO: Regular log',
    ];

    File::put($logPath, implode("\n", $entries));

    // Run the command
    $exit = Artisan::call('calendar:show-logs', [
        '--lines' => 50,
    ]);

    expect($exit)->toBe(0);

    $output = Artisan::output();
    expect($output)->toContain('Google Calendar & Booking Logs');
    expect($output)->toContain('[GoogleCalendar]');
    expect($output)->toContain('[Booking]');
    expect($output)->toContain('Found');
});
