#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "================================================================================"
echo "BOOKING ERROR DIAGNOSTIC"
echo "================================================================================"
echo "📂 Project root: $PROJECT_ROOT"
echo ""

cd "$PROJECT_ROOT"

echo "1. CHECK RECENT ERRORS"
echo "----------------------"
tail -200 storage/logs/laravel.log | grep -B 5 -A 30 "local.ERROR" | tail -50

echo ""
echo "2. CHECK BOOKING RELATED ERRORS"
echo "--------------------------------"
tail -200 storage/logs/laravel.log | grep -i "booking\|calendar\|google" | tail -20

echo ""
echo "3. CHECK GOOGLE API STATUS"
echo "--------------------------"
php artisan tinker --execute="
echo 'Google API Client: ' . (class_exists('Google_Client') ? '✓ Installed' : '✗ Missing') . PHP_EOL;
\$user = \App\Models\User::where('role', 'company_administrator')->whereNotNull('google_refresh_token')->first();
if (\$user) {
    echo 'Admin user: ' . \$user->email . PHP_EOL;
    echo 'Calendar ID: ' . (\$user->google_calendar_id ?? 'primary') . PHP_EOL;
    echo 'Has refresh token: ' . (!empty(\$user->google_refresh_token) ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo '✗ No admin with Google Calendar connected' . PHP_EOL;
}
"

echo ""
echo "4. TEST GOOGLE CALENDAR SERVICE"
echo "--------------------------------"
php artisan tinker --execute="
try {
    \$service = app(\App\Services\GoogleCalendarService::class);
    echo '✓ GoogleCalendarService can be instantiated' . PHP_EOL;
} catch (\Exception \$e) {
    echo '✗ Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "5. CHECK LAST WEB REQUEST"
echo "-------------------------"
tail -50 storage/logs/laravel.log | grep -A 5 "POST /book"

echo ""
echo "================================================================================"
