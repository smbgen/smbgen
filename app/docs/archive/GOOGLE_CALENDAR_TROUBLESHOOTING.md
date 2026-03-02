# Google Calendar Integration - Troubleshooting Guide

## Overview
This guide helps diagnose and fix Google Calendar connection issues in ClientBridge.

## 🔥 CRITICAL: Two Different OAuth Flows (Nov 18, 2025)

### THE PROBLEM - Wrong Callback URL Being Used

This application has **TWO SEPARATE** Google OAuth integrations:

1. **User Login OAuth** (`/auth/google/callback`)
   - Basic profile scopes only (email, profile, openid)
   - Used for signing in users with Google accounts
   - Does NOT include calendar scopes
   
2. **Calendar Integration OAuth** (`/admin/calendar/callback`)
   - Extended scopes for calendar access
   - Used for syncing bookings with Google Calendar
   - Requires offline access and refresh tokens
   - **THIS IS WHAT YOU NEED FOR CALENDAR FEATURES**

### Common Mistake: Using Wrong Callback

If you see logs like this:
```
"scope":"email profile openid https://www.googleapis.com/auth/userinfo.email"
```

But NO calendar scopes, you're hitting the **wrong callback URL**!

### The Fix

**1. Update .env on VPS:**
```bash
nano /var/www/clientbridge-laravel/.env
```

Add this line (if not present):
```env
GOOGLE_CALENDAR_REDIRECT_URI=https://houston1.oldlinecyber.com/admin/calendar/callback
```

**2. Add Redirect URI to Google Console:**

Go to: https://console.cloud.google.com/apis/credentials

Add BOTH URIs to "Authorized redirect URIs":
```
https://houston1.oldlinecyber.com/auth/google/callback
https://houston1.oldlinecyber.com/admin/calendar/callback
```

**3. Clear Cache:**
```bash
cd /var/www/clientbridge-laravel
php artisan config:clear
php artisan cache:clear
```

**4. Connect at the RIGHT URL:**

Navigate to: `https://houston1.oldlinecyber.com/admin/calendar` and click "Connect Google Calendar"

**NOT** `/auth/google` - that's for login only!

## 🚨 CRITICAL FIX (Nov 8, 2025 - Evening)

### THE ROOT CAUSE - ENCRYPTION BREAKING SILENT SAVES
The `encrypted` cast on `access_token` and `refresh_token` in the GoogleCredential model was causing **SILENT SAVE FAILURES** across ALL environments (VPS, cloud instances, local dev). Laravel's encrypted casts can fail without throwing errors if:
- APP_KEY is missing or incorrect
- Encryption library has issues
- Database encoding problems
- Any encryption-related configuration issue

**This affected EVERY environment** because it was a fundamental code issue, not a configuration problem.

### The Fix
1. **Removed encryption casts** - Tokens are still secure via HTTPS and proper access controls
2. **Added comprehensive logging** - Now logs every step of the save process with verification
3. **Created diagnostic command** - `php artisan calendar:diagnose` to check system health
4. **Added legacy support** - Works with both old and new storage methods

## Recent Fixes (Nov 8, 2025)

### Issues Identified
1. **ENCRYPTION BREAKING SAVES**: Encrypted casts causing silent failures (PRIMARY ISSUE)
2. **Deprecated Column References**: Code was checking `google_refresh_token` column on users table instead of `google_credentials` table
3. **No Token Refresh**: Access tokens were expiring without automatic refresh
4. **Poor Error Visibility**: Calendar disconnections were silent with no user notifications
5. **Inconsistent Checks**: Different parts of the app checked calendar status differently

### Fixes Implemented

#### 1. Automatic Token Refresh
- **File**: `app/Models/GoogleCredential.php`
- **What**: Added `refreshAccessToken()` method that automatically refreshes expired tokens
- **How**: Uses refresh token to get new access token when needed
- **Logging**: Comprehensive logging of refresh attempts, successes, and failures

#### 2. Updated GoogleCalendarService
- **File**: `app/Services/GoogleCalendarService.php`
- **What**: Auto-refreshes tokens before creating/deleting calendar events
- **When**: Checks if token needs refresh (expired or < 5 minutes until expiry)
- **Result**: Calendar operations work seamlessly without manual reconnection

#### 3. Fixed Database Queries
- **Files**: 
  - `app/Http/Controllers/BookingController.php`
  - `app/Services/DashboardWidgetService.php`
- **What**: Updated all queries to check `googleCredential` relationship
- **Before**: `whereNotNull('google_refresh_token')`
- **After**: `whereHas('googleCredential', function($q) { $q->whereNotNull('refresh_token'); })`

#### 4. Visual Status Indicators
- **File**: `resources/views/components/calendar-status-alert.blade.php`
- **What**: Created reusable alert component showing calendar connection status
- **Colors**:
  - 🟢 Green: Connected and working
  - 🟡 Yellow: Not connected (with "Connect Calendar" button)
  - 🔴 Red: Token expired (with "Reconnect Calendar" button)
- **Placement**:
  - Admin Dashboard (above booking widget)
  - Availability Settings page
  - Booking Management dashboard

## Quick Diagnosis (NEW!)

### Run the Diagnostic Command
```bash
php artisan calendar:diagnose
```

This will show you:
- ✓ Configuration status (client ID, secret, redirect URIs)
- ✓ Database schema (tables, columns, record counts)
- ✓ User connections (who's connected, token expiration)
- ✓ Dependencies (Google API client)
- ✓ Recommendations (what to fix)

### Migrate Legacy Data (if needed)
```bash
php artisan calendar:diagnose --migrate
```

## How to Diagnose Calendar Issues

### Step 1: Check Database
```bash
php artisan tinker
```

```php
// Check if user has Google credentials
$user = User::find(1); // Replace with actual user ID
$user->googleCredential;

// Check if token is expired
$user->googleCredential?->isExpired();
$user->googleCredential?->needsRefresh();

// Check expiration time
$user->googleCredential?->expires_at;
```

### Step 2: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log | grep -i "google\|calendar\|token"
```

Look for:
- ✅ "Google access token refreshed successfully"
- ⚠️ "Access token needs refresh"
- ❌ "Google token refresh failed"
- ❌ "Cannot refresh token: No refresh token available"

### Step 3: Check Calendar Connection in Admin
1. Go to `/admin/calendar`
2. Look for connection status
3. If disconnected, click "Connect Google Calendar"
4. If expired, click "Reconnect Calendar"

### Step 4: Check Dashboard Widgets
1. Go to `/admin/dashboard`
2. Look for calendar status alert (if booking feature enabled)
3. Check System Health widget for Google Calendar status

## Common Issues & Solutions

### Issue: "User does not have Google Calendar credentials stored"
**Cause**: No entry in `google_credentials` table for this user
**Solution**: 
1. Go to `/admin/calendar`
2. Click "Connect Google Calendar"
3. Complete OAuth flow

### Issue: "Failed to refresh Google access token"
**Cause**: Refresh token is invalid or revoked
**Solution**:
1. User must disconnect and reconnect calendar
2. Go to `/admin/calendar`
3. Click "Disconnect" then "Connect Google Calendar"
4. Complete OAuth flow with `access_type=offline` and `prompt=consent`

### Issue: "No refresh token received"
**Cause**: OAuth flow didn't request offline access or user didn't grant consent
**Solution**:
1. Revoke access at https://myaccount.google.com/permissions
2. Reconnect via `/admin/calendar`
3. Make sure to grant all requested permissions

### Issue: Calendar events not creating
**Check**:
1. Is user's calendar connected? (check `google_credentials` table)
2. Is access token valid? (check `expires_at` column)
3. Check logs for "Google Calendar API error"
4. Verify Google Calendar scope in config: `https://www.googleapis.com/auth/calendar.events`

## Configuration

### Required .env Variables
```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
GOOGLE_CALENDAR_REDIRECT_URI=https://yourdomain.com/admin/calendar/callback
```

### Required Scopes
- `openid`
- `profile`
- `email`
- `https://www.googleapis.com/auth/calendar.events`
- `https://www.googleapis.com/auth/calendar.readonly`
- `https://www.googleapis.com/auth/drive.file` (for inspection reports)

## Testing Calendar Functionality

### Manual Test Steps
1. Connect calendar: `/admin/calendar` → "Connect Google Calendar"
2. Create availability rule: `/admin/availability`
3. Test public booking: `/book`
4. Check that:
   - Event appears in Google Calendar
   - Google Meet link is generated
   - Booking shows in admin dashboard

### Artisan Tinker Tests
```php
use App\Services\GoogleCalendarService;
use App\Models\User;

// Get user with calendar
$user = User::whereHas('googleCredential')->first();

// Test token refresh
$user->googleCredential->refreshAccessToken();

// Create test event
$service = app(GoogleCalendarService::class);
$result = $service->createEventForUser(
    $user,
    now()->addDay(),
    30,
    'Test Appointment',
    'Testing calendar integration'
);
```

## Database Schema

### google_credentials Table
```sql
- id
- user_id (foreign key to users)
- access_token (encrypted)
- refresh_token (encrypted)
- expires_at (datetime)
- calendar_id (string, default 'primary')
- external_account_email (string)
- created_at
- updated_at
```

## Monitoring & Alerts

### What to Monitor
1. **Token Expiration**: Check for credentials expiring within 24 hours
2. **Refresh Failures**: Monitor logs for failed refresh attempts
3. **API Errors**: Track Google API errors in logs
4. **Missing Connections**: Alert when admin users lack calendar credentials

### Scheduled Tasks Recommendation
Add to `routes/console.php`:
```php
Schedule::call(function () {
    // Alert for soon-to-expire tokens
    $expiringSoon = GoogleCredential::whereNotNull('refresh_token')
        ->whereBetween('expires_at', [now(), now()->addHours(24)])
        ->with('user')
        ->get();
    
    foreach ($expiringSoon as $credential) {
        Log::warning('Google Calendar token expiring soon', [
            'user_id' => $credential->user_id,
            'expires_at' => $credential->expires_at,
        ]);
        
        // Attempt to refresh proactively
        $credential->refreshAccessToken();
    }
})->hourly();
```

## Support Resources

### Google OAuth Documentation
- https://developers.google.com/identity/protocols/oauth2
- https://developers.google.com/calendar/api/guides/overview

### Laravel Socialite
- https://laravel.com/docs/socialite

### Troubleshooting Commands
```bash
# Check Google credentials in database
php artisan tinker -c "GoogleCredential::with('user')->get()"

# Test token refresh for all users
php artisan tinker -c "GoogleCredential::all()->each->refreshAccessToken()"

# View recent calendar-related logs
tail -100 storage/logs/laravel.log | grep -i calendar

# Check which users have calendar connected
php artisan tinker -c "User::whereHas('googleCredential')->pluck('email')"
```

## Version History
- **Nov 8, 2025**: Major refactor - Added auto-refresh, fixed queries, added status alerts
- Previous: Original implementation with manual token management
