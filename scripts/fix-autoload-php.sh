#!/usr/bin/env bash

# Fix vendor/autoload.php Generation Issues on Windows
# Run this script if composer install fails with pre-autoload-dump errors

set -euo pipefail

echo "🔧 Fixing vendor/autoload.php Generation Issues"
echo "==============================================="
echo ""

# Check if we're in a Laravel project
if [[ ! -f "composer.json" ]] || [[ ! -f "artisan" ]]; then
    echo "❌ Error: This doesn't appear to be a Laravel project directory."
    echo "   Please run this script from the root of the Laravel project."
    exit 1
fi

echo "✅ Found Laravel project files"

# Check if vendor/autoload.php already exists
if [[ -f "vendor/autoload.php" ]]; then
    echo "✅ vendor/autoload.php already exists"
    echo "🔍 Issue might be elsewhere. Try running:"
    echo "   php artisan --version"
    exit 0
fi

echo "❌ vendor/autoload.php missing - fixing..."
echo ""

# Step 1: Create Laravel storage directories
echo "📁 Creating Laravel storage directories..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache  
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache
echo "✅ Directories created"

# Step 2: Determine which composer command to use
COMPOSER_CMD="composer"
if ! command -v composer >/dev/null 2>&1; then
    if command -v composer.bat >/dev/null 2>&1; then
        COMPOSER_CMD="composer.bat"
        echo "🔧 Using composer.bat"
    else
        echo "❌ Error: Neither 'composer' nor 'composer.bat' found"
        echo "   Please ensure Composer is installed and in PATH"
        exit 1
    fi
fi

# Step 3: Run composer install
echo "📦 Running composer install..."
if $COMPOSER_CMD install --optimize-autoloader; then
    echo "✅ Composer install successful"
else
    echo "⚠️  Composer install failed, trying without scripts..."
    if $COMPOSER_CMD install --optimize-autoloader --no-scripts; then
        echo "✅ Composer install successful (without scripts)"
        
        # Manual post-install tasks
        echo "🔧 Running manual post-install tasks..."
        if command -v php >/dev/null 2>&1; then
            PHP_CMD="php"
        elif command -v php.bat >/dev/null 2>&1; then
            PHP_CMD="php.bat"
        else
            echo "❌ Error: PHP not found"
            exit 1
        fi
        
        $PHP_CMD artisan package:discover --ansi
        echo "✅ Package discovery complete"
    else
        echo "❌ Composer install failed even without scripts"
        echo "🔧 Debug suggestions:"
        echo "   1. Check PHP is working: php -v"
        echo "   2. Check Composer is working: composer --version"
        echo "   3. Try manually: composer dump-autoload --optimize"
        exit 1
    fi
fi

# Step 4: Verify the fix
echo ""
echo "🔍 Verifying the fix..."
if [[ -f "vendor/autoload.php" ]]; then
    echo "✅ vendor/autoload.php now exists"
    
    # Test Laravel is working
    if command -v php >/dev/null 2>&1; then
        PHP_CMD="php"
    elif command -v php.bat >/dev/null 2>&1; then
        PHP_CMD="php.bat"
    fi
    
    if $PHP_CMD artisan --version >/dev/null 2>&1; then
        LARAVEL_VERSION=$($PHP_CMD artisan --version)
        echo "✅ Laravel is working: $LARAVEL_VERSION"
    else
        echo "⚠️  vendor/autoload.php exists but Laravel artisan not working"
        echo "   Try: php artisan key:generate"
    fi
else
    echo "❌ vendor/autoload.php still missing"
    echo "🔧 Manual steps to try:"
    echo "   1. composer dump-autoload --optimize"
    echo "   2. composer install --verbose (to see detailed error)"
    echo "   3. Check file permissions on vendor/ directory"
fi

echo ""
echo "🎉 Fix attempt complete!"
echo ""
echo "📋 If still having issues:"
echo "   1. Restart Git Bash completely"
echo "   2. Run the full setup script: bash scripts/setup-windows-herd-buildproject-and-run-dev.sh"
echo "   3. Check PHP/Composer setup: bash scripts/troubleshoot-herd-php.sh"