#!/bin/bash

echo "=== Restart Web Services ==="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ ERROR: This script must be run with sudo"
    echo "Usage: sudo ./deployment/restart-services.sh"
    exit 1
fi

echo "🔄 Restarting PHP-FPM..."
systemctl restart php8.4-fpm
if systemctl is-active --quiet php8.4-fpm; then
    echo "✅ PHP-FPM restarted successfully"
else
    echo "❌ PHP-FPM failed to start"
    systemctl status php8.4-fpm --no-pager
fi
echo ""

echo "🔄 Reloading Nginx..."
systemctl reload nginx
if systemctl is-active --quiet nginx; then
    echo "✅ Nginx reloaded successfully"
else
    echo "❌ Nginx failed to reload"
    systemctl status nginx --no-pager
fi
echo ""

echo "📊 Service Status:"
echo "-------------------"
echo -n "PHP-FPM: "
systemctl is-active php8.4-fpm && echo "✅ Running" || echo "❌ Stopped"
echo -n "Nginx: "
systemctl is-active nginx && echo "✅ Running" || echo "❌ Stopped"
echo ""

echo "🎉 Services restarted successfully!"
