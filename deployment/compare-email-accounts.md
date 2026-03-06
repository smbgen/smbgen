# Email Account Comparison: rtsenviro.com vs mail.oldlinecyber.com

## Current Situation
- **rtsenviro.com:465** - Works reliably ✅
- **mail.oldlinecyber.com:465** - Has issues ⚠️

## Likely Differences in cPanel Setup

### Authentication Method
The most common difference when creating email accounts in cPanel:

#### Option 1: Email Account (RECOMMENDED - what rtsenviro likely has)
```
cPanel > Email Accounts > Create Email Account
- Creates: user@domain.com
- Username for SMTP: user@domain.com (FULL EMAIL ADDRESS)
- Best practice for modern mail servers
- More secure and explicit
```

#### Option 2: Mail Account with Aliases (what oldlinecyber might have)
```
cPanel > Email Accounts > Create with custom settings
- Creates: user (without @domain.com)
- Username for SMTP: user (SHORT USERNAME)
- Older method, less compatible
- Can cause authentication issues
```

## How to Check & Fix in cPanel

### Step 1: Check Current Setup
1. Log into cPanel for oldlinecyber.com
2. Go to **Email Accounts**
3. Find the `booking@mail.oldlinecyber.com` account
4. Click **Manage** or **Check Email**
5. Look at the **Username** field

### Step 2: Verify Authentication Format
The issue is likely one of these:

**If Username shows:** `booking` (short form)
- ❌ PROBLEM: Need to use full email for SMTP
- Fix: Use `booking@mail.oldlinecyber.com` as MAIL_USERNAME in .env

**If Username shows:** `booking@mail.oldlinecyber.com` (full email)
- ✅ CORRECT: Already using proper format

### Step 3: Check Email Domain
**Current domain:** `mail.oldlinecyber.com`
**Possible issue:** Should it be `oldlinecyber.com` without the `mail.` prefix?

Check if the email account is:
- `booking@oldlinecyber.com` ← More likely correct
- `booking@mail.oldlinecyber.com` ← Less common (subdomain)

### Step 4: Compare SSL Certificates
Run this on VPS to check both certificates:
```bash
# Check rtsenviro.com (working)
echo | openssl s_client -connect rtsenviro.com:465 -servername rtsenviro.com 2>/dev/null | openssl x509 -noout -subject -issuer -dates

# Check mail.oldlinecyber.com (problematic)
echo | openssl s_client -connect mail.oldlinecyber.com:465 -servername mail.oldlinecyber.com 2>/dev/null | openssl x509 -noout -subject -issuer -dates
```

## Recommended Configuration for .env

### For rtsenviro.com (Working Example)
```env
MAIL_MAILER=smtp
MAIL_HOST=rtsenviro.com
MAIL_PORT=465
MAIL_USERNAME=booking@rtsenviro.com
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=booking@rtsenviro.com
MAIL_FROM_NAME="${APP_NAME}"
```

### For oldlinecyber.com (Try These Variations)

**Option A: Use main domain (not mail. subdomain)**
```env
MAIL_MAILER=smtp
MAIL_HOST=oldlinecyber.com
MAIL_PORT=465
MAIL_USERNAME=booking@oldlinecyber.com
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=booking@oldlinecyber.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Option B: Try port 587 with TLS**
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.oldlinecyber.com
MAIL_PORT=587
MAIL_USERNAME=booking@oldlinecyber.com
MAIL_PASSWORD=your_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=booking@oldlinecyber.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Quick Test Commands

### Test from VPS command line
```bash
# Test SMTP authentication
php artisan tinker
>>> Mail::raw('Test from tinker', function($msg) { $msg->to('your@email.com')->subject('Test'); });
>>> exit

# Check logs immediately
tail -20 storage/logs/laravel.log
```

### Test via admin panel
1. Go to: Email Deliverability
2. Click: Test SMTP
3. Review results for connectivity, SSL handshake, and auth

## Common cPanel Differences That Cause Issues

1. **Account Type**
   - POP3/IMAP only accounts (can't use SMTP)
   - Full email accounts (can use SMTP) ✅

2. **Quotas & Restrictions**
   - Check if account has "Unlimited" quota
   - Check if SMTP is enabled for the account

3. **Authentication Settings**
   - Some hosts require "Require SSL" to be checked
   - Some require specific authentication method (PLAIN, LOGIN, etc.)

4. **Domain Configuration**
   - MX records pointing correctly
   - SPF records including the mail server
   - DKIM configured

5. **Server-Level Restrictions**
   - IP-based restrictions (may block Laravel Cloud but allow VPS)
   - Rate limiting per account
   - Authentication attempts throttling

## Recommendations

1. **Recreate oldlinecyber email account** using same method as rtsenviro:
   - Delete current account
   - Create new account: `booking@oldlinecyber.com`
   - Set strong password
   - Enable all mail protocols (IMAP, POP3, SMTP)
   - Set quota to unlimited or high value

2. **Use consistent naming**:
   - Make sure FROM address matches USERNAME
   - Both should be full email addresses

3. **Test both configurations**:
   - Keep rtsenviro.com as primary (it works)
   - Set up oldlinecyber.com as backup/testing

4. **Add to .env**:
   ```env
   MAIL_VERIFY_PEER=false
   MAIL_VERIFY_PEER_NAME=false
   ```
   (Already in your config, which is why it works at all)

## Next Steps

1. Log into cPanel for oldlinecyber.com
2. Check the email account settings
3. Compare with rtsenviro.com setup
4. If different, recreate the account matching rtsenviro's configuration
5. Update VPS .env with correct settings
6. Test using admin panel Test SMTP feature
7. If still issues, try port 587 with TLS instead of 465 with SSL
