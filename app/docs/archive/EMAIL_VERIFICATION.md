# Email Verification System

## Overview
The client portal requires email verification to ensure secure access. Unverified users will see a verification gate overlay and cannot access any portal features until they verify their email address.

## User Experience

### For Unverified Users
When an unverified user logs in and tries to access the client portal, they will see:

1. **Verification Gate Overlay**
   - Full-screen semi-transparent overlay with backdrop blur
   - Dark themed card with yellow accent border
   - Envelope icon indicating email requirement
   - Clear message: "Verify Your Account"
   - Explanation about verification requirement

2. **Available Actions**
   - **Resend Verification Email** - Sends a new verification link to their email
   - **Logout** - Logs out and returns to login page

3. **Behind the Overlay**
   - Portal content is visible but blurred
   - All interactions are disabled (pointer-events: none)
   - User cannot access any functionality

### For Verified Users
- Full access to client portal
- No restrictions or overlays
- Can upload files, view documents, send messages, etc.

## Email Verification Flow

### Initial Registration
1. User registers with email/password
2. Verification email automatically sent
3. User clicks link in email → email_verified_at timestamp set
4. User can now access portal

### Manual Verification (Admin)
Admins can manually verify users from the admin panel:

1. Navigate to **Admin → User Management**
2. Find the user in the table
3. Click **Verify** button (green)
4. User's email_verified_at is set to current timestamp
5. User immediately gains portal access

### Unverifying Users (Admin)
Admins can revoke verification status:

1. Navigate to **Admin → User Management**
2. Find verified user
3. Click **Unverify** button (gray) and confirm
4. User's email_verified_at is set to null
5. User loses portal access until re-verified

## Technical Implementation

### Middleware
- Route Group: `Route::middleware(['auth', 'verified'])`
- All client portal routes protected with `verified` middleware
- Laravel's built-in `Illuminate\Auth\Middleware\EnsureEmailIsVerified`

### Routes
Protected routes in `routes/web.php`:
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/documents', ...)->name('client.files');
    // ... other client portal routes
});
```

Verification system routes in `routes/auth.php`:
- `GET /verify-email` - Verification prompt page
- `GET /verify-email/{id}/{hash}` - Verification link handler (signed)
- `POST /email/verification-notification` - Resend verification email (throttled: 6/min)

### Layout Integration
`resources/views/layouts/client.blade.php` checks verification status:

```blade
@if(auth()->check() && !auth()->user()->hasVerifiedEmail())
    <!-- Verification Gate Overlay -->
    <div class="fixed inset-0 bg-gray-900/95 backdrop-blur-md z-50">
        <!-- Verification card with resend button -->
    </div>
    <!-- Blurred portal content -->
    <div class="filter blur-lg pointer-events-none">
        @yield('content')
    </div>
@else
    @yield('content')
@endif
```

### User Model Methods
- `hasVerifiedEmail()` - Returns boolean, checks if email_verified_at is not null
- `markEmailAsVerified()` - Sets email_verified_at to current timestamp
- `sendEmailVerificationNotification()` - Sends verification email

### Controller Methods
**UserController** (Admin):
```php
public function verify(User $user)
{
    if (!auth()->user()->isAdministrator()) abort(403);
    $user->markEmailAsVerified();
    return redirect()->back()->with('success', 'User verified successfully.');
}

public function unverify(User $user)
{
    if (!auth()->user()->isAdministrator()) abort(403);
    $user->email_verified_at = null;
    $user->save();
    return redirect()->back()->with('success', 'User unverified successfully.');
}
```

## Email Throttling
- **Resend Limit:** 6 requests per minute per user
- Throttle enforced by Laravel's `throttle:6,1` middleware
- Prevents spam and abuse

## Security Considerations

### Why Verification is Required
1. **Prevents Fake Accounts** - Ensures real email addresses
2. **Secure Communication** - Admin messages reach correct users
3. **File Upload Security** - Only verified users can upload sensitive documents
4. **Identity Verification** - Confirms user identity before granting access

### Signed URLs
- Verification links use signed URLs (`signed` middleware)
- Links expire after configured time (default: 60 minutes)
- Cannot be tampered with or reused maliciously

### Rate Limiting
- Email resend is throttled to prevent abuse
- 6 attempts per minute per user
- Protects against email bombing attacks

## Testing Verification Gate

### Unverify a User for Testing
```php
php artisan tinker
$user = User::find(5); // Demo Client
$user->email_verified_at = null;
$user->save();
```

### Re-verify a User
```php
php artisan tinker
$user = User::find(5);
$user->markEmailAsVerified();
```

Or use the admin panel Verify button.

## User Documentation

### For Clients
**"Why can't I access the portal?"**

You need to verify your email address before accessing the client portal. Check your inbox (and spam folder) for an email from us with a verification link.

**"I didn't receive the verification email"**

1. Click the "Resend Verification Email" button on the portal gate
2. Check your spam/junk folder
3. Add our email address to your contacts
4. Contact support if you still don't receive it

**"The verification link expired"**

Verification links expire after 60 minutes for security. Click "Resend Verification Email" to receive a fresh link.

### For Administrators
**"A client says they can't access the portal"**

1. Log into admin panel
2. Go to User Management
3. Search for the user
4. Check if Email column shows a green checkmark (verified) or yellow warning (unverified)
5. Click **Verify** to manually verify them
6. Inform the client they can now access the portal

**"I need to revoke someone's portal access temporarily"**

1. Go to User Management
2. Find the user
3. Click **Unverify** and confirm
4. User will lose portal access immediately
5. You can re-verify them anytime by clicking **Verify**

## Troubleshooting

### Issue: User verified but still sees gate
**Cause:** Session cache issue  
**Solution:** User should logout and login again, or clear browser cache

### Issue: Verification email not sending
**Possible Causes:**
1. Mail driver not configured (check `.env` file)
2. SMTP credentials incorrect
3. Email throttling limit reached

**Solution:**
```bash
# Check mail configuration
php artisan tinker
config('mail.default');
config('mail.mailers.smtp');

# Test email sending
Mail::raw('Test', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

### Issue: Resend button shows "Too Many Attempts"
**Cause:** Rate limiting (6 per minute)  
**Solution:** User must wait 1 minute before trying again

## Configuration

### Email Settings
Configure in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@clientbridge.app
MAIL_FROM_NAME="${APP_NAME}"
```

### Verification Link Expiry
Configure in `config/auth.php`:
```php
'verification' => [
    'expire' => 60, // minutes
],
```

## Future Enhancements
- [ ] Add email verification bypass for trusted domains
- [ ] Send reminder emails to unverified users after X days
- [ ] Admin dashboard widget showing unverified user count
- [ ] Audit log for manual verifications/unverifications
- [ ] SMS verification as alternative to email
- [ ] Two-factor authentication integration

## Related Documentation
- [User Management](./USER_MANAGEMENT.md)
- [Client Portal](./CLIENT_PORTAL.md)
- [File Management](./FILE_MANAGEMENT.md)
- [Security Best Practices](./SECURITY.md)
