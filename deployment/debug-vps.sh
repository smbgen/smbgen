#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "=== ClientBridge VPS Debug Script ==="
echo "Run this on your VPS to diagnose deployment issues"
echo "=========================================="
echo "📂 Project root: $PROJECT_ROOT"
echo

# Change to project root
cd "$PROJECT_ROOT"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: Not in Laravel project directory"
    echo "Project root should be: $PROJECT_ROOT"
    exit 1
fi

echo "📂 Current Directory: $(pwd)"
echo

# Check PHP version
echo "🐘 PHP Version:"
php --version | head -1
echo

# Check if composer dependencies are installed
echo "📦 Composer Dependencies:"
if [ -d "vendor" ]; then
    echo "✅ vendor/ directory exists"
else
    echo "❌ vendor/ directory missing - run 'composer install'"
fi
echo

# Check Laravel version
echo "🔧 Laravel Version:"
php artisan --version 2>/dev/null || echo "❌ Cannot run artisan commands"
echo

# Check environment file
echo "⚙️  Environment Configuration:"
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    echo "APP_ENV: $(grep APP_ENV .env | cut -d'=' -f2)"
    echo "APP_DEBUG: $(grep APP_DEBUG .env | cut -d'=' -f2)"
    echo "APP_URL: $(grep APP_URL .env | cut -d'=' -f2)"
    
    # Check database config
    echo "DB_CONNECTION: $(grep DB_CONNECTION .env | cut -d'=' -f2)"
    echo "DB_DATABASE: $(grep DB_DATABASE .env | cut -d'=' -f2)"
else
    echo "❌ .env file missing - copy .env.example to .env"
fi
echo

# Check key generation
echo "🔑 Application Key:"
if grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "✅ Application key is set"
else
    echo "❌ Application key missing - run 'php artisan key:generate'"
fi
echo

# Check database connection
echo "🗄️  Database Connection:"
php artisan migrate:status 2>/dev/null | head -5 || echo "❌ Cannot connect to database"
echo

# Check critical directories and permissions
echo "📁 Directory Permissions:"
directories=("storage" "storage/logs" "storage/framework" "storage/framework/cache" "storage/framework/sessions" "storage/framework/views" "bootstrap/cache")

for dir in "${directories[@]}"; do
    if [ -d "$dir" ]; then
        perms=$(stat -c "%a" "$dir" 2>/dev/null || stat -f "%A" "$dir" 2>/dev/null)
        echo "✅ $dir (permissions: $perms)"
    else
        echo "❌ $dir missing"
    fi
done
echo

# Check web server configuration
echo "🌐 Web Server:"
if command -v nginx >/dev/null 2>&1; then
    echo "✅ Nginx installed"
    nginx -t 2>/dev/null && echo "✅ Nginx config valid" || echo "❌ Nginx config invalid"
else
    echo "⚠️  Nginx not found"
fi
echo

# Check PHP-FPM
if command -v php-fpm >/dev/null 2>&1; then
    echo "✅ PHP-FPM available"
    systemctl is-active php8.4-fpm >/dev/null 2>&1 && echo "✅ PHP-FPM running" || echo "❌ PHP-FPM not running"
else
    echo "⚠️  PHP-FPM not found"
fi
echo

# Check recent logs
echo "📋 Recent Laravel Logs:"
if [ -f "storage/logs/laravel.log" ]; then
    echo "Last 10 log entries:"
    tail -10 storage/logs/laravel.log
else
    echo "❌ No Laravel log file found"
fi
echo

# Check nginx error logs
echo "📋 Recent Nginx Error Logs:"
if [ -f "/var/log/nginx/error.log" ]; then
    echo "Last 5 nginx errors:"
    tail -5 /var/log/nginx/error.log
else
    echo "❌ Cannot access nginx error logs"
fi
echo

# Migration status
echo "🔄 Migration Status:"
php artisan migrate:status 2>/dev/null | tail -10 || echo "❌ Cannot check migrations"
echo

# Route debugging
echo "🛣️  Route Check:"
echo "Testing booking routes..."
php artisan route:list --name=booking 2>/dev/null || echo "❌ Cannot list routes"
echo

# Check Google Calendar integration
echo "📅 Google Calendar Config:"
if grep -q "GOOGLE_CLIENT_ID" .env 2>/dev/null; then
    echo "✅ Google OAuth credentials configured"
else
    echo "❌ Google OAuth credentials missing"
fi
echo

# Final recommendations
echo "🔧 COMMON FIXES:"
echo "1. Fix permissions: sudo chown -R www-data:www-data storage bootstrap/cache"
echo "2. Set permissions: sudo chmod -R 775 storage bootstrap/cache"
echo "3. Clear caches: php artisan cache:clear && php artisan config:clear && php artisan view:clear"
echo "4. Run migrations: php artisan migrate"
echo "5. Restart services: sudo systemctl restart nginx php8.4-fpm"
echo
echo "=== Debug Complete ==="