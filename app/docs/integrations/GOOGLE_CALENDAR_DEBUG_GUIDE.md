# Google Calendar Debugging Guide

## Quick Reference

**For quick lookup:** See [GOOGLE_CALENDAR_DEBUG_QUICKREF.md](GOOGLE_CALENDAR_DEBUG_QUICKREF.md)

## Overview

Comprehensive debugging logs have been added to track Google Calendar integration issues on production deployments.

## View Logs in Production

1. Open your hosting dashboard (Laravel Cloud, etc.)
2. Go to **Logs** section
3. Search for these tags:
   - `[GoogleCalendar]` - Calendar service operations
   - `[Booking]` - Booking flow tracking
   - `[GoogleCredential]` - Token refresh operations

## Local Log Viewing

**Watch logs in real-time:**
```bash
tail -f storage/logs/laravel.log | grep -E '\[GoogleCalendar\]|\[Booking\]|\[GoogleCredential\]'
```

**Filter specific booking:**
```bash
grep "booking_id.*123" storage/logs/laravel.log
```

## What Gets Logged

### [GoogleCalendar] Prefix
- Incoming parameters (user details, times, timezone, credentials status)
- Event time calculations (especially duration validation)
- API call data being sent to Google
- API response from Google
- Token refresh operations
- Detailed error info with stack traces

### [Booking] Prefix
- Incoming booking request data
- Selected admin and their Google Calendar credentials
- Call to calendar service
- Service response
- Success/failure outcome

### [GoogleCredential] Prefix
- Token expiration status
- Token refresh attempts
- Refresh response from Google
- Any refresh failures with error details

## Common Issues & Solutions

### Negative Duration Error
**Symptom:** Calendar event fails, logs show negative `time_difference_seconds`

**Cause:** Start time is after end time (booking wizard times are reversed)

**Fix:** Check booking form time inputs are validated correctly

### Token Expired
**Symptom:** `error: "invalid_grant"` in logs

**Cause:** Google OAuth token needs refresh

**Fix:** User must re-authenticate with Google Calendar

### Missing Credentials
**Symptom:** `has_refresh_token: false` in logs

**Cause:** Admin connected Google account but didn't grant calendar access

**Fix:** Admin must disconnect and reconnect Google account with proper scopes

### API Error
**Symptom:** `status_code: 400` in logs

**Cause:** Invalid event data sent to Google API

**Check:** 
- Duration is positive (end time > start time)
- Timezone is valid
- All required fields present

- **Success/Failure**: Detailed error logging if refresh fails

## How to View Logs in Laravel Cloud

### Method 1: Laravel Cloud Dashboard
```bash
# In your Laravel Cloud dashboard:
1. Navigate to your project
2. Go to the "Logs" section
3. Filter by time range when the issue occurred
4. Search for tags: [GoogleCalendar], [Booking], or [GoogleCredential]
```

### Method 2: CLI (if available)
```bash
php artisan cloud:logs --tail --filter="GoogleCalendar|Booking"
```

### Method 3: Direct Log File Access (if SSH available)
```bash
tail -f storage/logs/laravel.log | grep -E '\[GoogleCalendar\]|\[Booking\]|\[GoogleCredential\]'
```

## What to Look For

### 1. **Negative Duration Issue**
Look for the log entry:
```
[GoogleCalendar] Calculated event times
```

Check these fields:
- `time_difference_seconds` - Should be positive (e.g., 1800 for 30 minutes)
- `is_positive_duration` - Should be `true`
- `duration_minutes` - Should match expected booking duration

### 2. **Token Issues**
Look for:
```
[GoogleCredential] Token refresh response received
```

Check:
- `has_access_token` - Should be `true`
- `has_error` - Should be `false`
- `token_keys` - Should include 'access_token'

### 3. **API Errors**
Look for:
```
[GoogleCalendar] Google Calendar API Exception
```

Check:
- `status_code` - HTTP error code
- `errors` - Detailed error array from Google
- `start_time_iso` - Verify time format is correct

### 4. **Missing Credentials**
Look for:
```
[Booking] Admin with Google Calendar found
```

Check:
- `has_google_credential` - Should be `true`
- `credential_needs_refresh` - Shows if token needs refresh
- `calendar_id` - Should not be null

## Common Issues & Patterns

### Issue 1: Negative Duration
**Log Pattern:**
```
[GoogleCalendar] Calculated event times
  is_positive_duration: false
  time_difference_seconds: -X
```
**Cause:** Start time is after end time, or duration is negative

### Issue 2: Expired Token
**Log Pattern:**
```
[GoogleCredential] Google token refresh failed with error
  error: "invalid_grant"
```
**Cause:** Refresh token has been revoked or expired

### Issue 3: Timezone Mismatch
**Log Pattern:**
```
[GoogleCalendar] Calculated event times
  timezone: "UTC"
  app_timezone: "America/New_York"
```
**Cause:** Timezone configuration mismatch

### Issue 4: Missing Refresh Token
**Log Pattern:**
```
[GoogleCalendar] User missing credentials
  has_credential_model: true
  has_refresh_token: false
```
**Cause:** User needs to reconnect their Google Calendar

## Filtering Examples

### Get all Google Calendar related logs:
```bash
grep -E '\[GoogleCalendar\]|\[Booking\]|\[GoogleCredential\]' storage/logs/laravel.log
```

### Get only errors:
```bash
grep -E '\[GoogleCalendar\].*error|\[Booking\].*error' storage/logs/laravel.log
```

### Get logs for a specific booking:
```bash
grep "booking_id.*123" storage/logs/laravel.log | grep -E '\[GoogleCalendar\]|\[Booking\]'
```

### Get logs for a specific time range (in log viewer):
Filter by timestamp and search for `[GoogleCalendar]` or `[Booking]`

## Next Steps

1. **Reproduce the issue** on production
2. **Check logs immediately** after the issue occurs
3. **Look for the prefixed tags**: `[GoogleCalendar]`, `[Booking]`, `[GoogleCredential]`
4. **Compare values**: Check if times, durations, and timezones are correct
5. **Review error messages**: Google API errors are now fully logged with details

## Test on Local First

Before deploying, test locally:
```bash
# Trigger a booking and check logs
tail -f storage/logs/laravel.log

# Filter for debug logs
tail -f storage/logs/laravel.log | grep -E '\[GoogleCalendar\]|\[Booking\]'
```

## Report Format

When reporting issues, include:
1. **Timestamp** of the booking attempt
2. **All log entries** with `[GoogleCalendar]`, `[Booking]`, or `[GoogleCredential]` tags
3. **Booking ID** if available
4. **User ID** of the admin whose calendar was used
5. **Expected vs Actual** behavior

## Laravel Cloud Log Levels

Logs are at these levels:
- **Info**: Normal operation flow (tracking)
- **Warning**: Potential issues but non-critical
- **Error**: Failures that need attention

All debug logs use `Log::info()` and `Log::error()` for proper Laravel Cloud integration.
