#!/bin/bash
set -e

# Get the project root directory
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

echo "🔍 Checking Google OAuth Configuration on VPS..."
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    echo "   Copy .env.example to .env and configure it."
    exit 1
fi

echo "📋 Current Google OAuth Configuration:"
echo "========================================"

# Check each required Google OAuth variable
GOOGLE_CLIENT_ID=$(grep "^GOOGLE_CLIENT_ID=" .env | cut -d '=' -f2- | tr -d '"' | tr -d "'")
GOOGLE_CLIENT_SECRET=$(grep "^GOOGLE_CLIENT_SECRET=" .env | cut -d '=' -f2- | tr -d '"' | tr -d "'")
GOOGLE_REDIRECT_URI=$(grep "^GOOGLE_REDIRECT_URI=" .env | cut -d '=' -f2- | tr -d '"' | tr -d "'")

if [ -z "$GOOGLE_CLIENT_ID" ]; then
    echo "❌ GOOGLE_CLIENT_ID: MISSING"
else
    echo "✅ GOOGLE_CLIENT_ID: ${GOOGLE_CLIENT_ID:0:20}..."
fi

if [ -z "$GOOGLE_CLIENT_SECRET" ]; then
    echo "❌ GOOGLE_CLIENT_SECRET: MISSING"
else
    echo "✅ GOOGLE_CLIENT_SECRET: ${GOOGLE_CLIENT_SECRET:0:20}..."
fi

if [ -z "$GOOGLE_REDIRECT_URI" ]; then
    echo "❌ GOOGLE_REDIRECT_URI: MISSING"
else
    echo "✅ GOOGLE_REDIRECT_URI: $GOOGLE_REDIRECT_URI"
fi

echo ""
echo "📍 Expected Redirect URI for VPS:"
echo "   https://houston1.oldlinecyber.com/google/callback"
echo ""

# Check if any are missing
if [ -z "$GOOGLE_CLIENT_ID" ] || [ -z "$GOOGLE_CLIENT_SECRET" ] || [ -z "$GOOGLE_REDIRECT_URI" ]; then
    echo "❌ PROBLEM FOUND: Missing required Google OAuth credentials!"
    echo ""
    echo "📝 To fix this issue:"
    echo ""
    echo "1. Get your Google OAuth credentials from:"
    echo "   https://console.cloud.google.com/apis/credentials"
    echo ""
    echo "2. Make sure these redirect URIs are authorized:"
    echo "   - https://houston1.oldlinecyber.com/google/callback"
    echo "   - https://clientbridge-laravel.test/google/callback (for local dev)"
    echo ""
    echo "3. Add these to your VPS .env file:"
    echo "   GOOGLE_CLIENT_ID=your_client_id_here"
    echo "   GOOGLE_CLIENT_SECRET=your_client_secret_here"
    echo "   GOOGLE_REDIRECT_URI=https://houston1.oldlinecyber.com/google/callback"
    echo ""
    echo "4. After updating .env, run:"
    echo "   php artisan config:clear"
    echo "   php artisan config:cache"
    echo ""
    exit 1
fi

# Verify redirect URI matches expected
EXPECTED_URI="https://houston1.oldlinecyber.com/google/callback"
if [ "$GOOGLE_REDIRECT_URI" != "$EXPECTED_URI" ]; then
    echo "⚠️  WARNING: Redirect URI doesn't match expected value!"
    echo "   Current:  $GOOGLE_REDIRECT_URI"
    echo "   Expected: $EXPECTED_URI"
    echo ""
    echo "   Update your .env file with:"
    echo "   GOOGLE_REDIRECT_URI=$EXPECTED_URI"
    echo ""
fi

echo "✅ Google OAuth configuration looks good!"
echo ""
echo "📋 Next steps:"
echo "   1. Verify these credentials match your Google Cloud Console"
echo "   2. Ensure the redirect URI is authorized in Google Console"
echo "   3. Clear config cache: php artisan config:clear && php artisan config:cache"
echo ""
