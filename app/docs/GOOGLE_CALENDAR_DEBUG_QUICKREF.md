# 🔍 Quick Reference: Google Calendar Debug Logs

## Deploy & View Logs

### 1. Deploy to Laravel Cloud
```bash
git add .
git commit -m "Add comprehensive Google Calendar debug logging"
git push
```

### 2. View Logs in Laravel Cloud
- Open Laravel Cloud Dashboard → Your Project → **Logs**
- Search for these tags:
  - `[GoogleCalendar]` - Calendar service operations
  - `[Booking]` - Booking flow tracking
  - `[GoogleCredential]` - Token refresh operations

## Key Log Entries to Watch

### ✅ Successful Flow
```
[Booking] Processing new booking request
[Booking] Admin with Google Calendar found
[GoogleCalendar] createEventForUser called
[GoogleCalendar] Calculated event times → is_positive_duration: true
[GoogleCalendar] About to call Google Calendar API
[GoogleCalendar] Google Calendar API call succeeded
[Booking] Google Calendar event created successfully
```

### ❌ Common Issues

#### Negative Duration
```
[GoogleCalendar] Calculated event times
  time_difference_seconds: -1800  ← Should be positive!
  is_positive_duration: false     ← Should be true!
```

#### Token Expired
```
[GoogleCredential] Google token refresh failed with error
  error: "invalid_grant"
```

#### Missing Credentials
```
[GoogleCalendar] User missing credentials
  has_credential_model: true
  has_refresh_token: false
```

#### API Error
```
[GoogleCalendar] Google Calendar API Exception
  status_code: 400
  errors: [...detailed error array...]
```

## Critical Fields to Check

### Duration Validation
- `time_difference_seconds` - Must be > 0
- `is_positive_duration` - Must be `true`
- `duration_minutes` - Should match booking duration

### Time Formatting
- `starts_at_iso` - ISO 8601 format
- `timezone` - Should match your app timezone
- `current_server_time` - For time comparison

### Credentials
- `has_google_credential` - Must be `true`
- `has_refresh_token` - Must be `true`
- `credential_needs_refresh` - Shows if token expired

### API Response
- `created_event_id` - Should not be null
- `has_conference_data` - For Google Meet link
- `created_status` - Should be "confirmed"

## Filtering Commands

### Laravel Cloud (Non-Interactive Console)
```bash
# Show recent Google Calendar and Booking logs (default: last 200 lines)
php artisan calendar:show-logs

# Show more lines if needed
php artisan calendar:show-logs --lines=500

# For comprehensive diagnostics (connection status, tokens, etc.)
php artisan calendar:diagnose
```

### Local Development
```bash
# Use the same non-interactive command
php artisan calendar:show-logs --lines=200

# Or real-time watching (interactive)
tail -f storage/logs/laravel.log | grep -E '\[GoogleCalendar\]|\[Booking\]|\[GoogleCredential\]'

# All calendar logs
grep -E '\[GoogleCalendar\]|\[Booking\]|\[GoogleCredential\]' storage/logs/laravel.log

# Only errors
grep -E 'error.*\[GoogleCalendar\]|error.*\[Booking\]' storage/logs/laravel.log

# Specific booking
grep "booking_id.*123" storage/logs/laravel.log
```

## Test Before Deploying
```bash
# Create a test booking locally and watch logs
tail -f storage/logs/laravel.log | grep -E '\[GoogleCalendar\]|\[Booking\]'
```

## When Reporting Issues

Include these from the logs:
1. **Timestamp** of the failure
2. **All lines** with `[GoogleCalendar]`, `[Booking]`, `[GoogleCredential]` tags
3. **booking_id** if available
4. **user_id** of the admin
5. **Error messages** and stack traces

## Test Script
```bash
php scripts/test-google-calendar-logging.php
```

Shows:
- Users with Google Calendar connected
- Log configuration
- Sample log output
- Recent bookings

---

📚 **Full Documentation:**
- [GOOGLE_CALENDAR_DEBUG_GUIDE.md](GOOGLE_CALENDAR_DEBUG_GUIDE.md) - Complete debugging guide
- [GOOGLE_CALENDAR_DEBUG_SUMMARY.md](GOOGLE_CALENDAR_DEBUG_SUMMARY.md) - Implementation details
