#!/bin/bash

echo "=== Whitelist Your IP on VPS ==="
echo ""

# Get your current public IP
echo "🔍 Detecting your public IP address..."
YOUR_IP=$(curl -s https://api.ipify.org 2>/dev/null || curl -s ifconfig.me 2>/dev/null || curl -s icanhazip.com 2>/dev/null)

if [ -z "$YOUR_IP" ]; then
    echo "❌ Could not detect your IP address automatically"
    echo "Please enter your IP address manually:"
    read -p "Your IP: " YOUR_IP
fi

echo "✅ Your IP: $YOUR_IP"
echo ""

# VPS SSH connection details (from your VPS setup)
VPS_HOST="houston1.oldlinecyber.com"
VPS_USER="root"  # Change if using different user

echo "🔐 Connecting to VPS: $VPS_USER@$VPS_HOST"
echo ""

# Create the whitelist script to run on VPS
ssh $VPS_USER@$VPS_HOST << EOF
#!/bin/bash
echo "=== Adding IP $YOUR_IP to Nginx whitelist ==="

# Backup current nginx config
NGINX_CONF="/etc/nginx/sites-available/smbgen"
if [ -f "\$NGINX_CONF" ]; then
    cp "\$NGINX_CONF" "\$NGINX_CONF.backup.\$(date +%Y%m%d_%H%M%S)"
    echo "✅ Backed up Nginx config"
fi

# Check if IP is already whitelisted
if grep -q "$YOUR_IP" "\$NGINX_CONF" 2>/dev/null; then
    echo "✅ IP $YOUR_IP is already whitelisted"
else
    # Add IP to whitelist (find the location block and add allow directive)
    sed -i "/location \/ {/a\    allow $YOUR_IP;" "\$NGINX_CONF"
    echo "✅ Added IP $YOUR_IP to Nginx whitelist"
fi

# Test nginx configuration
echo ""
echo "🔍 Testing Nginx configuration..."
nginx -t

if [ \$? -eq 0 ]; then
    echo "✅ Nginx config is valid"
    echo ""
    echo "♻️  Reloading Nginx..."
    systemctl reload nginx
    echo "✅ Nginx reloaded successfully"
    echo ""
    echo "🎉 Your IP $YOUR_IP is now whitelisted!"
else
    echo "❌ Nginx config is invalid - restoring backup"
    cp "\$NGINX_CONF.backup.\$(date +%Y%m%d_%H%M%S)" "\$NGINX_CONF"
    echo "⚠️  Changes rolled back"
fi

echo ""
echo "📋 Current whitelist:"
grep "allow" "\$NGINX_CONF" | grep -v "#"

EOF

echo ""
echo "=== Whitelist Complete ==="
echo ""
echo "You can now access the VPS from IP: $YOUR_IP"
