#!/bin/bash

##############################################
# Google Calendar Connection Status Checker
##############################################
# This script checks the status of Google Calendar
# integration on the VPS and helps diagnose issues
##############################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Google Calendar Connection Status Checker    ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════╝${NC}"
echo ""

# Determine if we're on VPS or local
if [ -d "/var/www/smbgen-laravel" ]; then
    APP_DIR="/var/www/smbgen-laravel"
    IS_VPS=true
    echo -e "${GREEN}✓ Running on VPS${NC}"
else
    APP_DIR="$PWD"
    IS_VPS=false
    echo -e "${YELLOW}⚠ Running locally${NC}"
fi

echo -e "${BLUE}App Directory: ${APP_DIR}${NC}"
echo ""

# Check if artisan exists
if [ ! -f "$APP_DIR/artisan" ]; then
    echo -e "${RED}✗ Error: artisan not found in $APP_DIR${NC}"
    exit 1
fi

cd "$APP_DIR"

# Run the Laravel diagnostic command
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo -e "${YELLOW}Running Laravel Calendar Diagnostics...${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo ""

php artisan calendar:diagnose

echo ""
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo -e "${YELLOW}Additional System Checks${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo ""

# Check .env file for Google credentials
echo -e "${BLUE}🔍 Checking Environment Variables...${NC}"

if [ -f "$APP_DIR/.env" ]; then
    echo -e "${GREEN}✓ .env file exists${NC}"
    
    # Check for Google credentials (without showing values)
    if grep -q "^GOOGLE_CLIENT_ID=" "$APP_DIR/.env"; then
        CLIENT_ID=$(grep "^GOOGLE_CLIENT_ID=" "$APP_DIR/.env" | cut -d '=' -f2)
        if [ -n "$CLIENT_ID" ] && [ "$CLIENT_ID" != "your-google-client-id" ]; then
            echo -e "${GREEN}✓ GOOGLE_CLIENT_ID is set${NC}"
        else
            echo -e "${RED}✗ GOOGLE_CLIENT_ID is not configured or using placeholder${NC}"
        fi
    else
        echo -e "${RED}✗ GOOGLE_CLIENT_ID not found in .env${NC}"
    fi
    
    if grep -q "^GOOGLE_CLIENT_SECRET=" "$APP_DIR/.env"; then
        CLIENT_SECRET=$(grep "^GOOGLE_CLIENT_SECRET=" "$APP_DIR/.env" | cut -d '=' -f2)
        if [ -n "$CLIENT_SECRET" ] && [ "$CLIENT_SECRET" != "your-google-client-secret" ]; then
            echo -e "${GREEN}✓ GOOGLE_CLIENT_SECRET is set${NC}"
        else
            echo -e "${RED}✗ GOOGLE_CLIENT_SECRET is not configured or using placeholder${NC}"
        fi
    else
        echo -e "${RED}✗ GOOGLE_CLIENT_SECRET not found in .env${NC}"
    fi
    
    if grep -q "^GOOGLE_REDIRECT_URI=" "$APP_DIR/.env"; then
        REDIRECT_URI=$(grep "^GOOGLE_REDIRECT_URI=" "$APP_DIR/.env" | cut -d '=' -f2)
        echo -e "${BLUE}  Redirect URI: ${REDIRECT_URI}${NC}"
    fi
    
    if grep -q "^GOOGLE_CALENDAR_REDIRECT_URI=" "$APP_DIR/.env"; then
        CALENDAR_REDIRECT=$(grep "^GOOGLE_CALENDAR_REDIRECT_URI=" "$APP_DIR/.env" | cut -d '=' -f2)
        echo -e "${BLUE}  Calendar Redirect: ${CALENDAR_REDIRECT}${NC}"
    fi
else
    echo -e "${RED}✗ .env file not found${NC}"
fi

echo ""

# Check composer packages
echo -e "${BLUE}📦 Checking Dependencies...${NC}"

if [ -f "$APP_DIR/composer.json" ]; then
    if grep -q "google/apiclient" "$APP_DIR/composer.json"; then
        echo -e "${GREEN}✓ google/apiclient in composer.json${NC}"
        
        if [ -d "$APP_DIR/vendor/google/apiclient" ]; then
            echo -e "${GREEN}✓ google/apiclient installed in vendor${NC}"
        else
            echo -e "${RED}✗ google/apiclient not installed - run: composer install${NC}"
        fi
    else
        echo -e "${RED}✗ google/apiclient not in composer.json - run: composer require google/apiclient${NC}"
    fi
else
    echo -e "${RED}✗ composer.json not found${NC}"
fi

echo ""

# Check recent logs for Google Calendar errors
echo -e "${BLUE}📋 Recent Google Calendar Logs (last 20 entries)...${NC}"

if [ -f "$APP_DIR/storage/logs/laravel.log" ]; then
    echo ""
    grep -i "google\|calendar" "$APP_DIR/storage/logs/laravel.log" | tail -20 || echo -e "${YELLOW}No recent Google Calendar logs found${NC}"
else
    echo -e "${YELLOW}⚠ Log file not found${NC}"
fi

echo ""
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo -e "${YELLOW}Google Console Information${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo ""

echo -e "${BLUE}📍 Google Cloud Console URLs:${NC}"
echo ""
echo -e "  ${GREEN}Main Console:${NC}"
echo -e "    https://console.cloud.google.com/"
echo ""
echo -e "  ${GREEN}API & Services > Credentials:${NC}"
echo -e "    https://console.cloud.google.com/apis/credentials"
echo ""
echo -e "  ${GREEN}API & Services > OAuth consent screen:${NC}"
echo -e "    https://console.cloud.google.com/apis/credentials/consent"
echo ""
echo -e "  ${GREEN}API Library (Enable Calendar API):${NC}"
echo -e "    https://console.cloud.google.com/apis/library/calendar-json.googleapis.com"
echo ""

echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo -e "${YELLOW}Quick Fix Commands${NC}"
echo -e "${YELLOW}═══════════════════════════════════════════════${NC}"
echo ""

echo -e "${BLUE}If credentials need updating on VPS:${NC}"
echo "  nano .env"
echo "  # Update GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET"
echo "  php artisan config:clear"
echo "  php artisan cache:clear"
echo ""

echo -e "${BLUE}If user needs to reconnect:${NC}"
echo "  # Navigate to: https://your-domain.com/admin/calendar"
echo "  # Click 'Connect Google Calendar'"
echo ""

echo -e "${BLUE}If package is missing:${NC}"
echo "  composer require google/apiclient"
echo ""

echo -e "${BLUE}To test token refresh:${NC}"
echo "  php artisan tinker"
echo "  >>> \$user = User::find(1);"
echo "  >>> \$user->googleCredential->refreshAccessToken();"
echo ""

echo -e "${GREEN}✅ Diagnostic check complete!${NC}"
echo ""
