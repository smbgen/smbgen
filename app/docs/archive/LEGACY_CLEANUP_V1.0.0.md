# Legacy Google Connection Cleanup - v1.0.0

## Summary
Removed all legacy Google Calendar connection references and technical token details from the UI. Simplified the user experience to show only "Connected" or "Not Connected" status.

## Changes Made

### 1. Admin Google OAuth Page (`resources/views/admin/google-oauth.blade.php`)
**Removed:**
- "Legacy Mode" badges on user cards
- Token expiration timestamp displays (`expires_at->diffForHumans()`)
- "Needs Refresh" status indicators
- Entire legacy connection section showing users.google_refresh_token storage
- Yellow warning boxes about "Connected via legacy method"
- Legacy connection counting in statistics

**Simplified:**
- Status now shows simple "Connected" badge instead of token status
- Removed technical OAuth status details
- Stats only count GoogleCredential table connections

### 2. Admin Calendar Index (`resources/views/admin/calendar/index.blade.php`)
**Cleaned up in previous session:**
- Removed all token expiration displays
- Removed legacy connection checks
- Simplified to "Connected" or "Not Connected" with simple call-to-action

### 3. Calendar Status Alert Component (`resources/views/components/calendar-status-alert.blade.php`)
**Cleaned up in previous session:**
- Removed confusing countdown timers
- Only shows alert if connection is actually expired/missing
- No more "Expires: 2 minutes from now" confusion

### 4. Booking Wizard (`resources/views/book/wizard.blade.php`)
**Changed:**
- `$staff->google_refresh_token` → `$staff->googleCredential`
- Display label: "Google Token" → "Calendar"

### 5. Main Layout (`resources/views/layouts/app.blade.php`)
**Changed (3 instances):**
- `auth()->user()->google_refresh_token` → `auth()->user()->googleCredential`
- Connection status check
- Connected message display
- Reconnect button conditional

### 6. Availability Index (`resources/views/admin/availability/index.blade.php`)
**Changed:**
- "Refresh Token" label → "Connection"
- `auth()->user()->google_refresh_token` → `auth()->user()->googleCredential`
- Calendar ID now accessed via `googleCredential->calendar_id`
- Connection check in warning section

## Backend Changes

### 7. BookingController (`app/Http/Controllers/BookingController.php`)
**Changed (2 instances):**
- Line 227: `whereNotNull('google_refresh_token')` → `whereHas('googleCredential')`
- Line 309-323: Already fixed in previous session to use GoogleCredential relationship

### 8. ClientController (`app/Http/Controllers/ClientController.php`)
**Changed:**
- Line 192: `$admin->google_refresh_token` → `$admin->googleCredential`
- Lines 227-232: Replaced direct refresh token usage with GoogleCredential auto-refresh:
  ```php
  // OLD: $token = $clientGoogle->fetchAccessTokenWithRefreshToken($admin->google_refresh_token);
  
  // NEW:
  if ($admin->googleCredential->needsRefresh()) {
      $admin->googleCredential->refreshAccessToken();
  }
  $clientGoogle->setAccessToken($admin->googleCredential->access_token);
  ```

### 9. CalendarController (`app/Http/Controllers/Admin/CalendarController.php`)
**No changes needed:**
- Already correctly handles legacy field cleanup during disconnect
- Lines 183-192 intentionally clear old users.google_refresh_token column for migration purposes

## Not Changed (By Design)

### InspectionReportController & GoogleDriveService
**Reason:** These use Google Drive API (not Calendar) and have a different architecture that passes refresh tokens directly to GoogleDriveService methods. This would require refactoring the entire GoogleDriveService class to accept User/GoogleCredential objects instead of raw tokens.

**Recommendation:** Leave as-is for now since:
1. Inspection reports are a separate feature from calendar booking
2. Would require significant GoogleDriveService refactor
3. Functionality still works with legacy column
4. Can be addressed in future update if needed

## Database Schema

### Current State
**GoogleCredential Model (Preferred):**
- `user_id` - belongs to User
- `access_token` - short-lived (1 hour)
- `refresh_token` - permanent
- `expires_at` - auto-managed
- `external_account_email` - Google account email
- `calendar_id` - Calendar identifier

**Users Table (Legacy, Being Phased Out):**
- `google_refresh_token` - deprecated but not removed
- `google_calendar_id` - deprecated but not removed
- Still cleared by CalendarController->disconnect() for backward compatibility

## Migration Notes

### User Flow
1. Old users with `users.google_refresh_token` should reconnect via OAuth
2. New connections go directly to GoogleCredential table
3. UI no longer distinguishes between old/new storage - both hidden from user
4. Backend still cleans up legacy fields during disconnect

### Token Refresh
**Before:** Manual refresh in controllers using refresh token
**After:** Automatic via GoogleCredential->needsRefresh() and refreshAccessToken()

## Testing Recommendations

1. Verify admin calendar page shows simplified connection status
2. Verify google-oauth users page removed all "Legacy Mode" badges
3. Test booking flow with admin having GoogleCredential
4. Test client meeting creation works with new token refresh approach
5. Verify availability page shows correct connection status
6. Check navigation dropdown shows correct Google Services status

## Future Improvements

1. **Remove Legacy Columns:** After all users migrate to GoogleCredential table:
   - Drop `users.google_refresh_token`
   - Drop `users.google_calendar_id`
   - Remove cleanup code from CalendarController

2. **Refactor GoogleDriveService:** Update to use GoogleCredential model instead of raw refresh tokens

3. **Unified Google Service:** Consider merging Calendar and Drive OAuth flows into single unified connection

## Version Tag
Tagged as: `v1.0.0-stable`

## Documentation Updated
- This document (LEGACY_CLEANUP_V1.0.0.md)
- Previous session created GOOGLE_CALENDAR_TROUBLESHOOTING.md
- BOOKING_LEAD_INTEGRATION.md mentions email confirmations
