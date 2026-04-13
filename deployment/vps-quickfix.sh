#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "=== smbgen VPS Quick Fix Script ==="
echo "This will attempt to fix common deployment issues"
echo "=============================================="
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

echo "🔧 Applying common fixes..."
echo

# 1. Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo "✅ Caches cleared"
echo

# 2. Install/Update dependencies
echo "📦 Checking composer dependencies..."
composer install --no-dev --optimize-autoloader
echo "✅ Dependencies updated"
echo

# 3. Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force
echo "✅ Migrations complete"
echo

# 4. Create missing directories
echo "📁 Creating missing directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
echo "✅ Directories created"
echo

# 5. Generate app key if missing
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
    echo "✅ Application key generated"
else
    echo "✅ Application key already exists"
fi
echo

# 6. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimization complete"
echo

# 7. Test the application
echo "🧪 Testing application..."
php artisan route:list --name=booking | head -5
echo
echo "🎉 Quick fix complete!"
echo
echo "Next steps:"
echo "1. Run: sudo ./deployment/fix-permissions.sh"
echo "2. Run: sudo ./deployment/restart-services.sh"
echo "3. Check your domain/IP in browser"
echo "4. Test the booking page: /book"
echo "5. Check admin dashboard: /admin/dashboard"
echo "6. If still not working, run: ./deployment/debug-vps.sh"