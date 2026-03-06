#!/bin/bash

# Email Debugging Script for Laravel Application
# Checks email configuration, sends test emails, and verifies deliverability

echo "=============================================="
echo "  EMAIL DEBUGGING SCRIPT"
echo "=============================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}✗ .env file not found!${NC}"
    exit 1
fi

echo "📧 EMAIL CONFIGURATION"
echo "=============================================="

# Load and display mail configuration
echo ""
echo "MAIL_MAILER: $(grep MAIL_MAILER .env | cut -d '=' -f2)"
echo "MAIL_HOST: $(grep MAIL_HOST .env | cut -d '=' -f2)"
echo "MAIL_PORT: $(grep MAIL_PORT .env | cut -d '=' -f2)"
echo "MAIL_USERNAME: $(grep MAIL_USERNAME .env | cut -d '=' -f2)"
echo "MAIL_ENCRYPTION: $(grep MAIL_ENCRYPTION .env | cut -d '=' -f2)"
echo "MAIL_FROM_ADDRESS: $(grep MAIL_FROM_ADDRESS .env | cut -d '=' -f2)"
echo "MAIL_FROM_NAME: $(grep MAIL_FROM_NAME .env | cut -d '=' -f2)"
echo ""

# Check business email settings
echo "📋 BUSINESS EMAIL SETTINGS"
echo "=============================================="
echo ""
echo "BUSINESS_EMAIL: $(grep BUSINESS_EMAIL .env | cut -d '=' -f2)"
echo "BUSINESS_COMPANY_NAME: $(grep BUSINESS_COMPANY_NAME .env | cut -d '=' -f2)"
echo ""

# Check email log table
echo "📊 EMAIL LOG STATUS"
echo "=============================================="
echo ""
php artisan tinker --execute="echo 'Total emails logged: ' . \App\Models\EmailLog::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Sent: ' . \App\Models\EmailLog::where('status', 'sent')->count() . PHP_EOL;"
php artisan tinker --execute="echo 'Delivered: ' . \App\Models\EmailLog::where('status', 'delivered')->count() . PHP_EOL;"
php artisan tinker --execute="echo 'Opened: ' . \App\Models\EmailLog::where('status', 'opened')->count() . PHP_EOL;"
php artisan tinker --execute="echo 'Failed: ' . \App\Models\EmailLog::where('status', 'failed')->count() . PHP_EOL;"
php artisan tinker --execute="echo 'Bounced: ' . \App\Models\EmailLog::where('status', 'bounced')->count() . PHP_EOL;"
echo ""

# Check recent email logs
echo "📝 RECENT EMAIL LOGS (Last 5)"
echo "=============================================="
echo ""
php artisan tinker --execute="\$logs = \App\Models\EmailLog::latest()->take(5)->get(['id', 'to_email', 'subject', 'status', 'created_at']); foreach(\$logs as \$log) { echo \$log->id . ' | ' . \$log->to_email . ' | ' . \$log->status . ' | ' . \$log->created_at . PHP_EOL; }"
echo ""

# Check for recent errors
echo "⚠️  RECENT EMAIL ERRORS"
echo "=============================================="
echo ""
php artisan tinker --execute="\$errors = \App\Models\EmailLog::whereIn('status', ['failed', 'bounced'])->latest()->take(3)->get(['id', 'to_email', 'error_message', 'created_at']); if(\$errors->isEmpty()) { echo 'No recent errors' . PHP_EOL; } else { foreach(\$errors as \$err) { echo 'ID ' . \$err->id . ': ' . \$err->to_email . PHP_EOL . '  Error: ' . substr(\$err->error_message, 0, 100) . PHP_EOL; } }"
echo ""

# Test SMTP connection
echo "🔌 TESTING SMTP CONNECTION"
echo "=============================================="
echo ""

MAIL_HOST=$(grep MAIL_HOST .env | cut -d '=' -f2 | tr -d '"')
MAIL_PORT=$(grep MAIL_PORT .env | cut -d '=' -f2 | tr -d '"')

if [ ! -z "$MAIL_HOST" ] && [ ! -z "$MAIL_PORT" ]; then
    echo "Testing connection to $MAIL_HOST:$MAIL_PORT..."
    if timeout 5 bash -c "cat < /dev/null > /dev/tcp/$MAIL_HOST/$MAIL_PORT" 2>/dev/null; then
        echo -e "${GREEN}✓ SMTP server is reachable${NC}"
    else
        echo -e "${RED}✗ Cannot reach SMTP server${NC}"
        echo "  Check firewall rules and MAIL_HOST/MAIL_PORT settings"
    fi
else
    echo -e "${YELLOW}⚠ MAIL_HOST or MAIL_PORT not configured${NC}"
fi
echo ""

# Offer to send test email
echo "=============================================="
echo ""
read -p "Would you like to send a test email? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Enter recipient email address: " TEST_EMAIL
    if [ ! -z "$TEST_EMAIL" ]; then
        echo ""
        echo "Sending test email to $TEST_EMAIL..."
        php artisan email:test "$TEST_EMAIL" 2>&1
        echo ""
        echo -e "${GREEN}✓ Test email command executed!${NC}"
        echo "Check the email log at: /admin/email-logs"
    else
        echo "No email address provided, skipping test."
    fi
fi

echo ""
echo "=============================================="
echo "  DEBUGGING COMPLETE"
echo "=============================================="
echo ""
echo "📌 Quick Actions:"
echo "  • View email logs: php artisan tinker"
echo "    > \\App\\Models\\EmailLog::latest()->take(10)->get()"
echo ""
echo "  • Clear failed emails:"
echo "    > \\App\\Models\\EmailLog::where('status', 'failed')->delete()"
echo ""
echo "  • Send test email:"
echo "    > php artisan email:test your@email.com"
echo ""
echo "  • Check Laravel logs:"
echo "    > tail -f storage/logs/laravel.log"
echo ""
