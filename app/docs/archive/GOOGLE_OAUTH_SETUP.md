# Google OAuth Setup for Calendar Integration

This guide explains how to set up Google OAuth for both user authentication and Google Calendar integration in ClientBridge.

## Two Separate OAuth Flows

ClientBridge uses Google OAuth for two purposes:

1. **User Authentication** (`/auth/google/callback`)
   - Allows users to sign in with their Google account
   - Requires: `openid`, `profile`, `email` scopes

2. **Calendar Integration** (`/admin/calendar/callback`)
   - Allows admins to connect their Google Calendar for booking management
   - Requires: `openid`, `profile`, `email`, `calendar.events` scopes
   - Uses `access_type=offline` and `prompt=consent` for refresh tokens

## Google Cloud Console Setup

### 1. Create a Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Note the Project ID

### 2. Enable APIs

1. Go to **APIs & Services** > **Library**
2. Enable these APIs:
   - **Google+ API** (for authentication)
   - **Google Calendar API** (for calendar integration)

### 3. Create OAuth 2.0 Credentials

1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. Application type: **Web application**
4. Name: `ClientBridge` (or your app name)

### 4. Configure Authorized Redirect URIs

Add **BOTH** redirect URIs for **EACH** environment:

**Production (clientbridge.app):**
```
https://clientbridge.app/auth/google/callback
https://clientbridge.app/admin/calendar/callback
```

**Local Development:**
```
http://localhost:8000/auth/google/callback
http://localhost:8000/admin/calendar/callback
```

**Important**: 
- Use `https://` for production
- Use `http://localhost:8000` for local development
- URIs must match EXACTLY (including trailing slashes or lack thereof)
- You can add multiple redirect URIs to the same OAuth client (recommended)
- All environments can share the same GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET

### 5. Get Credentials

After creating the OAuth client:
- Copy the **Client ID**
- Copy the **Client Secret**
- Add them to your `.env` file

## Environment Configuration

### Production .env

```env
# Google OAuth Credentials
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here

# Redirect URIs
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
GOOGLE_CALENDAR_REDIRECT_URI=https://yourdomain.com/admin/calendar/callback
```

### Local Development .env

```env
# Google OAuth Credentials (same as production)
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here

# Redirect URIs (use localhost)
GOOGLE_REDIRECT_URI=http://clientbridge-laravel.test/auth/google/callback
GOOGLE_CALENDAR_REDIRECT_URI=http://clientbridge-laravel.test/admin/calendar/callback
```

**Note**: You'll need to add localhost URIs to Google Cloud Console too.

## OAuth Consent Screen

### 1. Configure Consent Screen

1. Go to **APIs & Services** > **OAuth consent screen**
2. User Type: **External** (unless you have Google Workspace)
3. Fill in required fields:
   - App name: `ClientBridge`
   - User support email: your email
   - Developer contact: your email

### 2. Scopes

Add these scopes:
- `openid`
- `profile`
- `email`
- `https://www.googleapis.com/auth/calendar.events`

### 3. Test Users (During Development)

While your app is in "Testing" mode:
1. Go to **Test users**
2. Add the Gmail addresses that need access
3. Only these users can authorize the app

### 4. Publishing (Production)

To allow any Google user to connect:
1. Complete all OAuth consent screen fields
2. Submit for verification (required for calendar scope)
3. Google will review (can take days/weeks)
4. Once approved, status changes to "Published"

**During Testing**: Users will see "This app isn't verified" warning. They can click:
- "Advanced" → "Go to ClientBridge (unsafe)"

## Testing the Connection

### 1. Test User Authentication

```bash
# Visit in browser
https://yourdomain.com/login

# Click "Sign in with Google"
# Should redirect to Google
# After authorizing, should redirect to /dashboard
```

### 2. Test Calendar Connection

```bash
# Login as admin
# Visit
https://yourdomain.com/admin/calendar

# Click "Connect Google Calendar"
# Should redirect to Google consent screen
# Should show calendar permissions
# After authorizing, should redirect back with success message
```

### 3. Debug Issues

Run the debug script on your server:

```bash
php debug_google_calendar.php
```

This checks:
- Environment variables are set
- Database columns exist
- User account status
- OAuth configuration
- Recent errors

## Common Issues

### Issue: redirect_uri_mismatch or "doesn't comply with Google's OAuth 2.0 policy"

**Error**: `Error 400: redirect_uri_mismatch` or "You can't sign in to this app because it doesn't comply with Google's OAuth 2.0 policy"

**Cause**: The redirect URI in your environment doesn't match what's registered in Google Cloud Console

**Fix**:
1. Check your environment variables (in Laravel Cloud or .env):
   - `GOOGLE_REDIRECT_URI=https://clientbridge.app/auth/google/callback`
   - `GOOGLE_CALENDAR_REDIRECT_URI=https://clientbridge.app/admin/calendar/callback`
2. Go to Google Cloud Console > Credentials > Your OAuth Client
3. Scroll to "Authorized redirect URIs"
4. Add the EXACT URIs (copy/paste from error message if shown):
   - `https://clientbridge.app/auth/google/callback`
   - `https://clientbridge.app/admin/calendar/callback`
5. Click **Save** (changes are immediate)
6. No trailing slashes unless both have them
7. Protocol must match exactly (http vs https)

### Issue: No refresh_token Received

**Error**: "No refresh token received"

**Cause**: Google only returns refresh_token on first authorization, or if you don't use `prompt=consent`

**Fix**:
1. Revoke app access: https://myaccount.google.com/permissions
2. Find your app and click "Remove Access"
3. Try connecting again - you'll see full consent screen
4. Refresh token should now be saved

### Issue: Access Denied (403)

**Error**: "Access to the requested resource was denied"

**Cause**: App is in testing mode and user isn't a test user

**Fix**:
1. Go to Google Cloud Console > OAuth consent screen
2. Add user's email to "Test users"
3. Or publish the app (requires verification)

### Issue: Calendar API Not Enabled

**Error**: "Calendar API has not been used in project"

**Cause**: Google Calendar API isn't enabled in your project

**Fix**:
1. Go to APIs & Services > Library
2. Search "Google Calendar API"
3. Click "Enable"
4. Try connecting again

## Verifying It Works

After successfully connecting:

```bash
# Check database
php artisan tinker
>>> $user = User::where('email', 'admin@example.com')->first();
>>> $user->google_refresh_token // Should show a token
>>> $user->google_calendar_id // Should show email or "primary"
```

### Test Creating a Booking

1. Go to public booking page: `https://yourdomain.com/book`
2. Select a time slot
3. Fill in booking details
4. Submit
5. Check Google Calendar - event should appear with Google Meet link!

## Security Best Practices

1. **Never commit credentials to git**
   - Use `.env` file (already in `.gitignore`)
   - Use environment variables in CI/CD

2. **Use HTTPS in production**
   - Google requires HTTPS for production OAuth
   - Get SSL certificate (Let's Encrypt is free)

3. **Rotate secrets regularly**
   - If credentials leak, revoke and regenerate in Google Cloud Console

4. **Limit test users during development**
   - Only add trusted users to test user list

5. **Monitor OAuth usage**
   - Check Google Cloud Console for unusual activity

## Resources

- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google Calendar API](https://developers.google.com/calendar)
- [Laravel Socialite](https://laravel.com/docs/socialite)

## Need Help?

Run the diagnostic:
```bash
php debug_google_calendar.php
```

Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

The logs will show detailed error messages when OAuth fails.
