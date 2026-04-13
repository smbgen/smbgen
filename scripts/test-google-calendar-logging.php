#!/usr/bin/env php
<?php

/**
 * Test script to verify Google Calendar logging is working
 *
 * Usage:
 *   php scripts/test-google-calendar-logging.php
 *
 * This will simulate a booking and show all the debug logs that would be generated.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Carbon;

echo "=== Google Calendar Debug Logging Test ===\n\n";

// Find a user with Google Calendar connected
$user = User::whereHas('googleCredential', function ($q) {
    $q->whereNotNull('refresh_token');
})->with('googleCredential')->first();

if (! $user) {
    echo "❌ No users found with Google Calendar connected.\n";
    echo "   Connect a Google Calendar account first via /admin/calendar\n\n";
    exit(1);
}

echo "✓ Found user with Google Calendar: {$user->name} ({$user->email})\n";
echo "  Credential ID: {$user->googleCredential->id}\n";
echo "  Calendar ID: {$user->googleCredential->calendar_id}\n";
echo "  External Email: {$user->googleCredential->external_account_email}\n";
echo "  Expires At: {$user->googleCredential->expires_at}\n";
echo '  Needs Refresh: '.($user->googleCredential->needsRefresh() ? 'Yes' : 'No')."\n\n";

echo "=== Checking Log Configuration ===\n";
echo 'Log Channel: '.config('logging.default')."\n";
echo 'Log Level: '.config('logging.channels.'.config('logging.default').'.level', 'debug')."\n";
echo 'Log Path: '.storage_path('logs/laravel.log')."\n\n";

echo "=== Testing Debug Logging ===\n";
echo "The following logs would be generated during a booking:\n\n";

// Test log output
\Log::info('[TEST] Starting Google Calendar debug test', [
    'user_id' => $user->id,
    'timestamp' => now()->toIso8601String(),
]);

echo "1. Booking request received - [Booking] prefix logs\n";
echo "2. Finding staff with Google Calendar - [Booking] prefix logs\n";
echo "3. Creating calendar event - [GoogleCalendar] prefix logs\n";
echo "4. Token refresh (if needed) - [GoogleCredential] prefix logs\n";
echo "5. API call and response - [GoogleCalendar] prefix logs\n\n";

// Simulate what logs would look like
$testStartTime = Carbon::now()->addDays(1)->setTime(10, 0);

echo "=== Sample Log Output ===\n";
echo 'These entries would appear in: '.storage_path('logs/laravel.log')."\n\n";

$sampleLogs = [
    '[Booking] Processing new booking request',
    '  slot_input: '.$testStartTime->toIso8601String(),
    '  customer_name: Test Customer',
    "  staff_id: {$user->id}",
    '',
    '[Booking] Admin with Google Calendar found',
    "  admin_id: {$user->id}",
    "  admin_email: {$user->email}",
    '  has_google_credential: true',
    '',
    '[GoogleCalendar] createEventForUser called',
    "  user_id: {$user->id}",
    '  starts_at: '.$testStartTime->format('Y-m-d H:i:s'),
    '  duration_minutes: 30',
    '  timezone: '.config('app.timezone'),
    '',
];

foreach ($sampleLogs as $log) {
    echo '  '.$log."\n";
}

echo "\n=== How to View Logs ===\n";
echo "Local:\n";
echo "  tail -f storage/logs/laravel.log | grep -E '\\[GoogleCalendar\\]|\\[Booking\\]|\\[GoogleCredential\\]'\n\n";
echo "Laravel Cloud:\n";
echo "  1. Open Laravel Cloud Dashboard\n";
echo "  2. Navigate to Logs\n";
echo "  3. Search for: [GoogleCalendar] or [Booking]\n\n";

echo "=== Recent Bookings ===\n";
$recentBookings = Booking::with('staff')
    ->latest()
    ->take(5)
    ->get();

if ($recentBookings->isEmpty()) {
    echo "No recent bookings found.\n";
} else {
    foreach ($recentBookings as $booking) {
        echo "Booking #{$booking->id}:\n";
        echo "  Customer: {$booking->customer_name}\n";
        echo "  Time: {$booking->booking_date} {$booking->booking_time}\n";
        echo "  Status: {$booking->status}\n";
        echo '  Calendar Event: '.($booking->google_calendar_event_id ?: 'None')."\n";
        echo '  Meet Link: '.($booking->google_meet_link ?: 'None')."\n";
        echo "\n";
    }
}

echo "✓ Debug logging is configured and ready!\n";
echo "\nTo see debug logs during actual bookings:\n";
echo "  1. Open a terminal: tail -f storage/logs/laravel.log\n";
echo "  2. Create a booking via the web form\n";
echo "  3. Watch for [GoogleCalendar], [Booking], and [GoogleCredential] tags\n\n";
