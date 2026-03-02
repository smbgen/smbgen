#!/bin/bash

##############################################
# Deploy Booking Email Feature to VPS
##############################################

set -e

echo "═══════════════════════════════════════════════"
echo "Deploying Booking Confirmation Email Feature"
echo "═══════════════════════════════════════════════"
echo ""

# Determine if we're on VPS or local
if [ -d "/var/www/clientbridge-laravel" ]; then
    APP_DIR="/var/www/clientbridge-laravel"
    IS_VPS=true
    echo "✓ Running on VPS"
else
    echo "✗ This script should be run on the VPS"
    exit 1
fi

cd "$APP_DIR"

echo ""
echo "1. Pulling latest code from repository..."
git pull origin main

echo ""
echo "2. Installing/updating dependencies..."
composer install --no-dev --optimize-autoloader

echo ""
echo "3. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "4. Verifying email configuration..."
if grep -q "^MAIL_MAILER=" .env; then
    echo "✓ Mail driver configured"
    MAIL_DRIVER=$(grep "^MAIL_MAILER=" .env | cut -d '=' -f2)
    echo "  Using: $MAIL_DRIVER"
else
    echo "⚠ Warning: MAIL_MAILER not configured in .env"
    echo "  Add these to .env:"
    echo "    MAIL_MAILER=smtp"
    echo "    MAIL_HOST=your-smtp-host"
    echo "    MAIL_PORT=587"
    echo "    MAIL_USERNAME=your-username"
    echo "    MAIL_PASSWORD=your-password"
fi

echo ""
echo "5. Testing email configuration..."
php artisan tinker --execute="
try {
    echo 'Mail driver: ' . config('mail.default') . PHP_EOL;
    echo 'From address: ' . config('mail.from.address') . PHP_EOL;
    echo 'From name: ' . config('mail.from.name') . PHP_EOL;
    echo '✓ Email configuration loaded successfully' . PHP_EOL;
} catch (Exception \$e) {
    echo '✗ Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "6. Checking for recent bookings..."
php artisan tinker --execute="
\$count = App\Models\Booking::where('created_at', '>=', now()->subDays(7))->count();
echo 'Bookings in last 7 days: ' . \$count . PHP_EOL;
"

echo ""
echo "═══════════════════════════════════════════════"
echo "Deployment Complete!"
echo "═══════════════════════════════════════════════"
echo ""
echo "📧 What's New:"
echo "  • Automatic confirmation emails sent to customers"
echo "  • Includes appointment details and Google Meet link"
echo "  • Works even if Google Calendar notification fails"
echo ""
echo "🧪 Testing:"
echo "  1. Create a test booking at: https://houston1.oldlinecyber.com/book"
echo "  2. Check customer email for confirmation"
echo "  3. Check logs: tail -50 storage/logs/laravel.log | grep 'Booking confirmation'"
echo ""
echo "📋 To manually send a test email:"
echo "  php artisan tinker"
echo "  >>> \$booking = App\Models\Booking::latest()->first();"
echo "  >>> \$staffName = 'Test Staff';"
echo "  >>> Mail::to('your-test@email.com')->send(new App\Mail\BookingConfirmation(\$booking, \$booking->google_meet_link, \$staffName));"
echo ""
echo "✅ All set!"
echo ""
