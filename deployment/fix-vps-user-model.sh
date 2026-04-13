#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "=== VPS User Model Fix Script ==="
echo "This will fix the missing availabilities() method error"
echo "==================================="
echo "📂 Project root: $PROJECT_ROOT"
echo

# Change to project root
cd "$PROJECT_ROOT"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: Not in Laravel project directory"
    exit 1
fi

echo "🔄 Step 1: Pull latest code from repository"
echo "-------------------------------------------"
git status
echo
echo "Pulling latest changes..."
git pull origin main
echo "✅ Code updated"
echo

echo "🧹 Step 2: Clear all caches"
echo "---------------------------"
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo "✅ Caches cleared"
echo

echo "📦 Step 3: Update composer dependencies"
echo "--------------------------------------"
composer install --no-dev --optimize-autoloader
echo "✅ Dependencies updated"
echo

echo "🗄️  Step 4: Run database migrations"
echo "-----------------------------------"
php artisan migrate --force
echo "✅ Migrations complete"
echo

echo "⚡ Step 5: Optimize for production"
echo "---------------------------------"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimization complete"
echo

echo "🔧 Step 6: Fix permissions"
echo "--------------------------"
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
echo "✅ Permissions fixed"
echo

echo "🔄 Step 7: Restart services"
echo "---------------------------"
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
echo "✅ Services restarted"
echo

echo "🧪 Step 8: Test the User model"
echo "------------------------------"
php artisan tinker --execute="
try {
    \$user = \App\Models\User::where('role', 'company_administrator')->first();
    if (\$user) {
        echo 'User found: ' . \$user->email . PHP_EOL;
        
        // Test if availabilities method exists
        \$availabilities = \$user->availabilities();
        echo '✅ availabilities() method works!' . PHP_EOL;
        
        // Test the booking query that was failing
        \$staffWithAvail = \App\Models\User::where('role', 'company_administrator')
            ->whereNotNull('google_refresh_token')
            ->whereHas('availabilities', function(\$q) {
                \q->where('is_active', true);
            })
            ->get();
        echo '✅ Staff query works! Found: ' . \$staffWithAvail->count() . ' staff members' . PHP_EOL;
        
    } else {
        echo '❌ No admin user found' . PHP_EOL;
    }
} catch (\Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo
echo "🎯 Step 9: Test booking page"
echo "----------------------------"
echo "Testing booking page availability..."
if curl -I http://localhost/book 2>/dev/null | head -1 | grep -q "200\|302"; then
    echo "✅ Booking page is accessible"
else
    echo "❌ Booking page not accessible - check nginx config"
fi

echo
echo "🎉 Fix Complete!"
echo "==============="
echo "The VPS should now have the updated User model with the availabilities() method."
echo
echo "Next steps:"
echo "1. Test the booking page in your browser"
echo "2. If still not working, check: tail -f storage/logs/laravel.log"
echo "3. Verify Google Calendar is connected in admin panel"
echo
echo "If you still see errors, run: ./debug-vps.sh"