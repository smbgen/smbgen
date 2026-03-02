# Email System Audit & Recommendations

## Current Status

### Laravel Cloud (rtsenviro.com) - ✅ WORKING
- **SMTP Server**: rtsenviro.com:465
- **Encryption**: SSL
- **Username**: smbgen@rtsenviro.com
- **From Address**: noreply@rtsenviro.com
- **From Name**: RTS Environmental Consulting
- **Status**: Working correctly

### VPS (mail.oldlinecyber.com) - ❌ FAILING
- **SMTP Server**: mail.oldlinecyber.com:465
- **Encryption**: SSL
- **Username**: noreply@oldlinecyber.com
- **From Address**: noreply@oldlinecyber.com
- **From Name**: smbgen
- **Status**: SSL handshake failing in PHP

## Root Cause Analysis

### Test Results
Both mail servers are:
- ✅ Reachable on ports 465 and 587
- ✅ Have valid SSL certificates (Let's Encrypt)
- ✅ OpenSSL handshake succeeds
- ❌ PHP stream_socket_client fails

### The Issue
PHP's `stream_socket_client()` has stricter SSL requirements than OpenSSL CLI. The error suggests:
1. Missing or incorrect `encryption` setting in config/mail.php
2. PHP's OpenSSL may need stream context options
3. Certificate hostname verification may be failing

## Fixes Implemented

### 1. Added Missing Encryption Setting
`config/mail.php` was missing the `encryption` parameter. Added:
```php
'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
```

### 2. Added SSL Verification Controls
Added optional SSL verification settings:
```php
'verify_peer' => env('MAIL_VERIFY_PEER', true),
'verify_peer_name' => env('MAIL_VERIFY_PEER_NAME', true),
```

## Testing Instructions

### Step 1: Clear Config Cache on VPS
```bash
php artisan config:clear
php artisan config:cache
```

### Step 2: Test Email Sending
```bash
php artisan email:test alexramse92@gmail.com
```

### Step 3: Check Results
```bash
# View recent email logs
php artisan tinker --execute="\\App\\Models\\EmailLog::latest()->take(5)->get(['id', 'to_email', 'status', 'error_message']);"

# Check Laravel logs
tail -20 storage/logs/laravel.log | grep -i mail
```

## If Still Failing

### Option A: Disable SSL Verification (Quick Fix)
Add to VPS `.env`:
```env
MAIL_VERIFY_PEER=false
MAIL_VERIFY_PEER_NAME=false
```

**Note**: Less secure but will work with self-signed or misconfigured certificates.

### Option B: Switch to TLS (Recommended)
Update VPS `.env`:
```env
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

### Option C: Use Laravel Cloud Mail Settings
Copy working settings from Laravel Cloud to VPS:
```env
MAIL_HOST=rtsenviro.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=smbgen@rtsenviro.com
MAIL_PASSWORD="B%)Q[9gRcuI8ms&+"
MAIL_FROM_ADDRESS="noreply@rtsenviro.com"
MAIL_FROM_NAME="RTS Environmental Consulting"
```

## Email Tracking Improvements

### Issues Fixed
1. ✅ Booking confirmation emails now tracked in email_logs table
2. ✅ Email tracking service integrated
3. ✅ Open/click tracking enabled
4. ✅ Better error logging with full stack traces

### Monitoring
- Admin panel: `/admin/email-logs`
- Filter by status: sent, delivered, opened, clicked, failed, bounced
- View detailed error messages and SMTP responses

## Recommendations

### Immediate Actions
1. Deploy config/mail.php changes to VPS
2. Clear config cache
3. Test email sending
4. Monitor `/admin/email-logs` for delivery status

### Long-term Improvements
1. **Use Single Mail Server**: Consider using rtsenviro.com for both environments
2. **Email Queue**: Implement queued emails for better reliability
3. **SPF/DKIM/DMARC**: Configure DNS records for better deliverability
4. **Monitoring**: Set up alerts for failed emails
5. **Backup Mailer**: Configure failover mailer in config/mail.php

### Environment Parity
Both environments should use identical mail settings where possible. Current differences:
- ❌ Different SMTP servers (rtsenviro.com vs mail.oldlinecyber.com)
- ❌ Different from addresses
- ❌ Missing BUSINESS_COMPANY_NAME on VPS

**Recommendation**: Use rtsenviro.com SMTP for both environments with proper from address configuration.

## DNS Records to Check

### For rtsenviro.com
```bash
dig rtsenviro.com MX
dig rtsenviro.com TXT | grep spf
dig default._domainkey.rtsenviro.com TXT
```

### For oldlinecyber.com
```bash
dig oldlinecyber.com MX
dig oldlinecyber.com TXT | grep spf
dig default._domainkey.oldlinecyber.com TXT
```

Ensure SPF includes: `a mx ip4:<your-server-ip>`

## Support Contacts

**Hosting Provider**: nixihost.com (both servers hosted there)
- Contact them if SSL issues persist
- Ask about their SMTP authentication requirements
- Verify firewall rules allow outbound SMTP

## Testing Commands Reference

```bash
# Test SMTP connection
openssl s_client -connect mail.oldlinecyber.com:465 -quiet

# Test with STARTTLS
openssl s_client -connect mail.oldlinecyber.com:587 -starttls smtp -quiet

# Send test email
php artisan email:test your@email.com

# View email logs
php artisan tinker
\App\Models\EmailLog::latest()->take(10)->get();

# Check failed emails
\App\Models\EmailLog::where('status', 'failed')->latest()->get(['id', 'to_email', 'error_message']);

# Clear failed emails
\App\Models\EmailLog::where('status', 'failed')->delete();
```
