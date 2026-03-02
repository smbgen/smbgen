#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "=== Fix Laravel Permissions ==="
echo "📂 Project root: $PROJECT_ROOT"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ ERROR: This script must be run with sudo"
    echo "Usage: sudo ./deployment/fix-permissions.sh"
    exit 1
fi

# Change to project root
cd "$PROJECT_ROOT"

echo "🔐 Setting file ownership to www-data..."
chown -R www-data:www-data storage bootstrap/cache
echo "✅ Ownership set"
echo ""

echo "📁 Setting directory permissions (775)..."
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions set"
echo ""

echo "📂 Ensuring storage subdirectories exist..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
echo "✅ Directories created"
echo ""

echo "🔐 Setting subdirectory ownership..."
chown -R www-data:www-data storage/framework storage/logs bootstrap/cache
echo "✅ Subdirectory ownership set"
echo ""

echo "🎉 Permissions fixed successfully!"
echo ""
echo "Storage structure:"
ls -la storage/ | grep -E "^d"
echo ""
echo "Bootstrap cache:"
ls -la bootstrap/cache 2>/dev/null || echo "(empty)"
