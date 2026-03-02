#!/bin/bash
set -e

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "🚀 Deploying ClientBridge to VPS..."
echo "📂 Project root: $PROJECT_ROOT"
echo ""

# Change to project root
cd "$PROJECT_ROOT"

# Pull latest code
echo "📦 Pulling latest code from git..."
git pull origin main
echo ""

# Install/update composer dependencies
echo "📚 Installing dependencies..."
composer install --no-dev --optimize-autoloader
echo ""

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force
echo ""

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo ""

# Rebuild caches
echo "🔧 Building optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo ""

# Build frontend assets (if npm is available)
if command -v npm &> /dev/null; then
    echo "🎨 Building frontend assets..."
    npm install
    npm run build
    echo ""
else
    echo "⚠️  npm not found, skipping frontend build"
    echo ""
fi

# Send deployment notification
echo "📧 Sending deployment notification..."
php artisan deploy:notify --commits=5
echo ""

echo "✅ Deployment complete!"
echo ""
echo "📋 Next steps:"
echo "  1. Run: sudo ./deployment/fix-permissions.sh"
echo "  2. Check .env file for correct configuration"
echo "  3. Test the site: https://houston1.oldlinecyber.com"
echo "  4. Check logs: tail -f storage/logs/laravel.log"
echo ""
