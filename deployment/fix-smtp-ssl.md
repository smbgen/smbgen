# Fix SMTP SSL Connection Issues

## Error
```
Connection could not be established with host "ssl://mail.oldlinecyber.com:465": stream_socket_client
```

## Root Cause
The SMTP server is reachable, but the SSL/TLS handshake is failing. This is typically due to:
1. SSL certificate verification issues
2. Cipher mismatch
3. PHP OpenSSL configuration

## Solutions

### Option 1: Try TLS on Port 587 (Recommended)
Update `.env`:
```env
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

### Option 2: Verify SSL Certificate
Add to `config/mail.php` in the mailer configuration:
```php
'smtp' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', 'localhost'),
    'port' => env('MAIL_PORT', 587),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'timeout' => null,
    'local_domain' => env('MAIL_EHLO_DOMAIN'),
    'verify_peer' => false, // Add this for self-signed certificates
],
```

### Option 3: Check PHP OpenSSL
```bash
# Verify OpenSSL is available
php -r "var_dump(extension_loaded('openssl'));"

# Check available ciphers
php -r "print_r(openssl_get_cipher_methods());"
```

### Option 4: Test with Different Settings
```bash
# Test with TLS
php artisan tinker
Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });

# Check logs immediately
tail -20 storage/logs/laravel.log
```

### Option 5: Use Log Driver for Testing
Temporarily use the log driver to verify email generation works:
```env
MAIL_MAILER=log
```
Emails will be written to `storage/logs/laravel.log` instead of being sent.

## VPS-Specific Issue: Missing BUSINESS_COMPANY_NAME

Your VPS `.env` is missing the company name. Add:
```env
BUSINESS_COMPANY_NAME="RTS Environmental Consulting"
```

Then clear config cache:
```bash
php artisan config:clear
php artisan config:cache
```

## Quick Test Commands

```bash
# Test with detailed error output
php artisan tinker --execute="
try {
    Mail::raw('Test email body', function(\$message) {
        \$message->to('alexramse92@gmail.com')
                ->subject('SMTP Test from ClientBridge');
    });
    echo 'Email sent successfully' . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
    echo 'Trace: ' . \$e->getTraceAsString() . PHP_EOL;
}
"
```

## Check Mail Server Requirements

Contact your hosting provider (oldlinecyber.com) to verify:
- Correct SMTP host and port
- SSL/TLS version requirements
- Whether they require specific authentication
- Firewall rules allowing outbound connections on ports 465/587
