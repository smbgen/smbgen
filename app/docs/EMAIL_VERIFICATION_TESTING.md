# Email Verification Testing Guide

## What Was Fixed

Email verification was completely broken. Here's what we fixed:

1. **Verification emails weren't being sent** - Added explicit calls to `sendEmailVerificationNotification()`
2. **Admin routes didn't require verification** - Added 'verified' middleware to all admin and super admin routes
3. **Wrong redirect after registration** - Changed to redirect to verification notice instead of dashboard

---

## Testing Steps

### Prerequisites

1. **Email Configuration**: Ensure `.env` has valid SMTP settings:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io  # Use Mailtrap for testing
   MAIL_PORT=2525
   MAIL_USERNAME=your-mailtrap-username
   MAIL_PASSWORD=your-mailtrap-password
   MAIL_FROM_ADDRESS="noreply@clientbridge.app"
   MAIL_FROM_NAME="CLIENTBRIDGE"
   ```

2. **Test Email Account**: Use [Mailtrap.io](https://mailtrap.io) or similar for testing
   - Create free account
   - Copy SMTP credentials to `.env`
   - All emails will be captured (not sent to real users)

3. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

---

## Test Case 1: New User Registration

### Steps:
1. Navigate to `/register`
2. Fill out registration form:
   - Name: Test User
   - Email: test@example.com
   - Password: Password123!
   - Confirm Password: Password123!
3. Click "Register"

### Expected Results:
✅ User is created in database
✅ User is logged in
✅ **User is redirected to `/verify-email` page**
✅ Verification page shows: "Thanks for signing up! Before getting started, please verify your email..."
✅ **Verification email is sent** (check Mailtrap inbox)
✅ Trying to access `/dashboard` redirects back to `/verify-email`

### Verification Email Should Contain:
- Subject: "Verify Email Address"
- Body: Link to verify email
- Link format: `http://yourdomain.com/verify-email/{id}/{hash}`

### After Clicking Verification Link:
✅ User is marked as verified in database (`email_verified_at` is set)
✅ User is redirected to dashboard
✅ Success message: "Email verified successfully!"
✅ User can now access all routes

---

## Test Case 2: Trial Signup

### Steps:
1. Navigate to `/trial`
2. Fill out trial signup form:
   - Company Name: Test Company
   - Name: Test Admin
   - Email: admin@example.com
   - Password: Password123!
   - Confirm Password: Password123!
3. Click "Start Free Trial"

### Expected Results:
✅ Tenant is created
✅ User is created with role 'company_administrator'
✅ User is logged in
✅ **User is redirected to `/verify-email` page**
✅ Success message: "Welcome! Please verify your email to start your 14-day trial."
✅ **Verification email is sent** (check Mailtrap inbox)
✅ Trying to access `/admin/dashboard` redirects to `/verify-email`

### After Clicking Verification Link:
✅ User is marked as verified
✅ User is redirected to `/admin/dashboard`
✅ Can now access all admin features

---

## Test Case 3: Login with Unverified Email

### Steps:
1. Register a new user (don't verify email)
2. Logout
3. Login again with same credentials

### Expected Results:
✅ Login succeeds (authentication works)
✅ **User is redirected to `/verify-email` page**
✅ User cannot access dashboard until verified
✅ Can request new verification email by clicking "Resend Verification Email"

---

## Test Case 4: Resend Verification Email

### Steps:
1. Register new user (or login with unverified account)
2. On verification notice page, click "Resend Verification Email"

### Expected Results:
✅ New verification email is sent
✅ Success message: "A new verification link has been sent to your email address."
✅ Previous verification link still works (or is invalidated depending on Laravel config)
✅ New link in email works

---

## Test Case 5: Google OAuth Signup

### Steps:
1. Click "Sign in with Google"
2. Authenticate with Google
3. User is created/logged in

### Expected Results:
✅ User is created with `email_verified_at` already set (Google emails are verified)
✅ User is redirected to dashboard immediately (no verification needed)
✅ User can access all features without email verification

**Why?** Google OAuth users have already verified their email with Google, so we trust that verification.

---

## Test Case 6: Direct Access to Protected Routes

### Test accessing these routes WITHOUT verification:

1. **Dashboard**: `/dashboard`
   - ✅ Redirects to `/verify-email`

2. **Admin Dashboard**: `/admin/dashboard`
   - ✅ Redirects to `/verify-email`

3. **Super Admin**: `/super-admin`
   - ✅ Redirects to `/verify-email`

4. **Profile**: `/profile`
   - ✅ Redirects to `/verify-email`

All protected routes should redirect unverified users to the verification notice.

---

## Test Case 7: Verification Link Validation

### Steps:
1. Get verification link from email
2. Test these scenarios:

#### Valid Link:
- Click link once
- ✅ Email is verified
- ✅ Redirect to dashboard with success message

#### Already Verified:
- Click link again after verification
- ✅ Shows: "Email already verified"
- ✅ Redirects to dashboard

#### Invalid Hash:
- Modify the hash in the URL
- ✅ Shows error: "Invalid verification link"
- ✅ Stays on verification notice page

#### Expired Link (if configured):
- Wait for link to expire (check `config/auth.php` for timeout)
- ✅ Shows error about expiration
- ✅ Can request new verification email

---

## Troubleshooting

### Verification Email Not Received

**Check Mailtrap/Email Logs:**
```bash
tail -f storage/logs/laravel.log | grep -i "verification"
```

Look for:
- ✅ "Verification email sent" log entry
- ❌ "Failed to send verification email" error

**Common Issues:**
1. **SMTP credentials wrong** - Check `.env` settings
2. **Queue not running** - If using queues, run `php artisan queue:work`
3. **Mail service down** - Check SMTP server status
4. **Firewall blocking** - Check if port 587/2525 is open

### User Can Access Dashboard Without Verification

**Check Route Middleware:**
```bash
php artisan route:list | grep dashboard
```

Should show:
```
GET /dashboard ............ auth,verified
GET /admin/dashboard ...... auth,verified,companyAdministrator
```

If 'verified' is missing, the middleware wasn't applied.

### Verification Link Shows 404

**Check Route Registration:**
```bash
php artisan route:list | grep verification
```

Should show:
```
GET verify-email .......................... verification.notice
GET verify-email/{id}/{hash} ............. verification.verify
POST email/verification-notification ...... verification.send
```

---

## Manual Database Testing

### Check User Verification Status:

```sql
SELECT id, name, email, email_verified_at, role, created_at
FROM users
WHERE email = 'test@example.com';
```

**Unverified user:**
- `email_verified_at` is `NULL`

**Verified user:**
- `email_verified_at` has a timestamp

### Manually Verify User (for testing):

```sql
UPDATE users
SET email_verified_at = NOW()
WHERE email = 'test@example.com';
```

### Manually Unverify User (for testing):

```sql
UPDATE users
SET email_verified_at = NULL
WHERE email = 'test@example.com';
```

---

## Expected Logs

### Successful Verification Email Send:

```
[2026-01-29 12:34:56] local.INFO: Verification email sent {"user_id":1,"email":"test@example.com"}
```

### Failed Verification Email Send:

```
[2026-01-29 12:34:56] local.ERROR: Failed to send verification email {"user_id":1,"error":"Connection refused"}
```

### Email Verified:

```
[2026-01-29 12:35:30] local.INFO: User verified their email address {"user_id":1}
```

---

## Production Considerations

### 1. Email Configuration

In production, use a reliable email service:
- **Recommended**: Postmark, SendGrid, Mailgun, Amazon SES
- Configure DNS records (SPF, DKIM, DMARC)
- Test deliverability before launch

### 2. Verification Link Expiration

Default: 60 minutes (configured in `config/auth.php`)

```php
'verification' => [
    'expire' => 60, // minutes
],
```

Consider increasing for production if users might not check email immediately.

### 3. Rate Limiting

Verification email resend is rate-limited to 6 attempts per minute.

See: `routes/auth.php` line 46:
```php
->middleware('throttle:6,1')
```

### 4. Monitoring

Monitor these metrics in production:
- Verification email send rate
- Verification completion rate
- Time between registration and verification
- Failed verification attempts

---

## Success Criteria

✅ All new registrations send verification email
✅ Trial signups send verification email
✅ Unverified users cannot access dashboard
✅ Unverified users cannot access admin panel
✅ Verification links work correctly
✅ Resend verification email works
✅ Google OAuth users are auto-verified
✅ Error handling and logging work
✅ User experience is clear and friendly

---

## Next Steps

After testing is complete:

1. **Update Email Templates**: Customize `resources/views/vendor/mail/` if needed
2. **Branding**: Add company logo to verification emails
3. **Copy**: Review email copy for your brand voice
4. **Monitoring**: Set up alerts for failed verification emails
5. **Documentation**: Update user onboarding docs to mention verification
