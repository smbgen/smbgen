#!/bin/bash

# Detailed SMTP Testing Script
# Tests both mail servers with OpenSSL to diagnose SSL/TLS issues

echo "=============================================="
echo "  DETAILED SMTP CONNECTION TEST"
echo "=============================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Test function
test_smtp() {
    local HOST=$1
    local PORT=$2
    local PROTOCOL=$3
    
    echo "Testing: $HOST:$PORT ($PROTOCOL)"
    echo "----------------------------------------------"
    
    # Test basic connectivity
    if timeout 5 bash -c "cat < /dev/null > /dev/tcp/$HOST/$PORT" 2>/dev/null; then
        echo -e "${GREEN}✓ Port is reachable${NC}"
    else
        echo -e "${RED}✗ Port is not reachable${NC}"
        echo ""
        return 1
    fi
    
    # Test SSL/TLS handshake with OpenSSL (matching PHP's verify_peer=false behavior)
    echo ""
    echo "Testing SSL/TLS handshake (certificate verification disabled)..."
    
    if [ "$PROTOCOL" = "ssl" ]; then
        # Direct SSL connection (port 465) - no verification like PHP
        RESULT=$(echo "QUIT" | timeout 10 openssl s_client -connect $HOST:$PORT -quiet 2>&1)
    else
        # STARTTLS connection (port 587) - no verification like PHP
        RESULT=$(echo "QUIT" | timeout 10 openssl s_client -connect $HOST:$PORT -starttls smtp -quiet 2>&1)
    fi
    
    if echo "$RESULT" | grep -q "^220"; then
        echo -e "${GREEN}✓ SSL/TLS handshake successful${NC}"
        echo "SMTP Banner: $(echo "$RESULT" | grep "^220" | head -1)"
        echo -e "${GREEN}✓ Server is accepting connections (matching PHP behavior)${NC}"
    elif echo "$RESULT" | grep -q "Connected"; then
        echo -e "${GREEN}✓ SSL/TLS connected${NC}"
        echo -e "${YELLOW}⚠ No SMTP banner received (may still work)${NC}"
    else
        echo -e "${RED}✗ SSL/TLS handshake failed${NC}"
        echo "Error details:"
        echo "$RESULT" | head -20
    fi
    
    echo ""
}

echo "TEST 1: RTS Enviro Mail Server (WORKING)"
echo "=============================================="
test_smtp "rtsenviro.com" "465" "ssl"

echo ""
echo "TEST 2: Old Line Cyber Mail Server (FAILING)"
echo "=============================================="
test_smtp "mail.oldlinecyber.com" "465" "ssl"

echo ""
echo "TEST 3: Old Line Cyber with TLS on 587"
echo "=============================================="
test_smtp "mail.oldlinecyber.com" "587" "tls"

echo ""
echo "=============================================="
echo "  DIAGNOSIS & RECOMMENDATIONS"
echo "=============================================="
echo ""
echo -e "${GREEN}✓ Certificate verification is DISABLED in config/mail.php${NC}"
echo "  This test matches your production PHP configuration."
echo ""
echo "Based on the test results above:"
echo ""
echo "If both servers show successful handshake:"
echo "  → Both mail servers are working correctly"
echo "  → Certificate warnings are ignored (as configured)"
echo "  → Email sending should work"
echo ""
echo "If emails still fail to send:"
echo "  → Check authentication credentials in .env"
echo "  → Verify MAIL_USERNAME matches FROM address"
echo "  → Check Laravel logs: tail -f storage/logs/laravel.log"
echo "  → Test via admin panel: Email Deliverability > Test SMTP"
echo "  → Test via CLI: php artisan email:test your@email.com"
echo ""
