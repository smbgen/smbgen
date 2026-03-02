#!/usr/bin/env bash

# Fix "bootstrap\cache directory must be present and writable" Error on Windows
# This is a common error when setting up Laravel on Windows

set -euo pipefail

echo "🔧 Fixing Bootstrap Cache Directory Error"
echo "=========================================="
echo ""

# Check if we're in a Laravel project
if [[ ! -f "composer.json" ]] || [[ ! -f "artisan" ]]; then
    echo "❌ Error: This doesn't appear to be a Laravel project directory."
    echo "   Please run this script from the root of the Laravel project."
    exit 1
fi

echo "✅ Found Laravel project files"
echo ""

# Step 1: Check current state
echo "🔍 Checking bootstrap directory..."
if [[ -d "bootstrap" ]]; then
    echo "✅ bootstrap/ directory exists"
else
    echo "❌ bootstrap/ directory missing - creating it..."
    mkdir bootstrap
fi

if [[ -d "bootstrap/cache" ]]; then
    echo "✅ bootstrap/cache/ directory exists"
    echo "🧹 Cleaning existing cache files..."
    
    # Remove any problematic temporary files
    rm -f bootstrap/cache/*.tmp 2>/dev/null || true
    rm -f bootstrap/cache/ser*.tmp 2>/dev/null || true
    
    # Keep .gitignore but remove compiled PHP cache files
    find bootstrap/cache -name "*.php" ! -name ".gitignore" -delete 2>/dev/null || true
    
    echo "✅ Cache cleaned"
else
    echo "❌ bootstrap/cache/ directory missing - creating it..."
    mkdir -p bootstrap/cache
    
    if [[ -d "bootstrap/cache" ]]; then
        echo "✅ bootstrap/cache/ created successfully"
    else
        echo "❌ Failed to create bootstrap/cache/"
        echo "💡 Trying alternative method..."
        
        # Try creating parent directory first
        mkdir bootstrap 2>/dev/null || true
        mkdir bootstrap/cache 2>/dev/null || true
        
        if [[ ! -d "bootstrap/cache" ]]; then
            echo "❌ Still unable to create bootstrap/cache/"
            echo ""
            echo "🔧 Manual steps to try:"
            echo "   1. Open PowerShell as Administrator"
            echo "   2. Run: New-Item -Path \"bootstrap\\cache\" -ItemType Directory -Force"
            echo "   3. Run: icacls \"bootstrap\\cache\" /grant Everyone:F /T"
            exit 1
        fi
    fi
fi

echo ""

# Step 2: Create .gitignore if missing
if [[ ! -f "bootstrap/cache/.gitignore" ]]; then
    echo "📝 Creating .gitignore in bootstrap/cache..."
    cat > bootstrap/cache/.gitignore << 'EOF'
*
!.gitignore
EOF
    echo "✅ .gitignore created"
fi

echo ""

# Step 3: Test write permissions
echo "🔍 Testing write permissions..."
TEST_FILE="bootstrap/cache/write-test-$(date +%s).tmp"

if touch "$TEST_FILE" 2>/dev/null; then
    echo "✅ bootstrap/cache is writable"
    rm -f "$TEST_FILE" 2>/dev/null || true
else
    echo "❌ bootstrap/cache is NOT writable"
    echo ""
    echo "🔧 Fixing permissions..."
    
    # Try chmod (works in Git Bash)
    chmod -R 775 bootstrap/cache 2>/dev/null && echo "✅ Permissions set via chmod" || {
        echo "⚠️  chmod failed (normal on Windows)"
        echo ""
        echo "💡 Windows-specific permission fixes (run in PowerShell):"
        echo ""
        echo "   Method 1 - Remove read-only attribute (RECOMMENDED):"
        echo "   attrib -r +a .\\bootstrap\\cache"
        echo ""
        echo "   Method 2 - Grant full permissions:"
        echo "   icacls \"bootstrap\\cache\" /grant Everyone:F /T"
        echo ""
        echo "   Method 3 - Command Prompt alternative:"
        echo "   attrib -r +a bootstrap\\cache"
        echo ""
    }
fi

echo ""

# Step 4: Create other required directories
echo "📁 Ensuring all Laravel directories exist..."
mkdir -p storage/framework/views 2>/dev/null || true
mkdir -p storage/framework/cache 2>/dev/null || true
mkdir -p storage/framework/sessions 2>/dev/null || true
mkdir -p storage/logs 2>/dev/null || true

# Verify all directories
REQUIRED_DIRS=(
    "storage/framework/views"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/logs"
    "bootstrap/cache"
)

echo ""
echo "🔍 Verifying all required directories..."
ALL_GOOD=true
for dir in "${REQUIRED_DIRS[@]}"; do
    if [[ -d "$dir" ]]; then
        echo "   ✅ $dir"
    else
        echo "   ❌ $dir (missing)"
        ALL_GOOD=false
    fi
done

echo ""

if [[ "$ALL_GOOD" == "true" ]]; then
    echo "🎉 All directories are present!"
    echo ""
    echo "📋 Next steps:"
    echo "   1. Run: composer install"
    echo "   2. Run: php artisan key:generate"
    echo "   3. Run: php artisan config:clear"
    echo "   4. Run: php artisan cache:clear"
    echo ""
    echo "💡 If you still see the error, try:"
    echo "   - Restart Git Bash completely"
    echo "   - Run as Administrator"
    echo "   - Check antivirus isn't blocking directory creation"
else
    echo "⚠️  Some directories are still missing"
    echo ""
    echo "🔧 Manual fix required:"
    echo "   1. Open PowerShell as Administrator"
    echo "   2. Navigate to your project directory"
    echo "   3. Run these commands:"
    echo ""
    for dir in "${REQUIRED_DIRS[@]}"; do
        if [[ ! -d "$dir" ]]; then
            echo "      New-Item -Path \"$dir\" -ItemType Directory -Force"
        fi
    done
    echo ""
    exit 1
fi

echo "✅ Bootstrap cache directory fix complete!"