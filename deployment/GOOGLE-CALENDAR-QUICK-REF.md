# Google Calendar - Quick Reference Card

## 🔗 Important URLs

### Google Cloud Console
- **Main Console:** https://console.cloud.google.com/
- **API Credentials:** https://console.cloud.google.com/apis/credentials
- **OAuth Consent Screen:** https://console.cloud.google.com/apis/credentials/consent
- **Enable Calendar API:** https://console.cloud.google.com/apis/library/calendar-json.googleapis.com
- **Revoke Access:** https://myaccount.google.com/permissions

### Your VPS URLs
- **Calendar Connection:** https://houston1.oldlinecyber.com/admin/calendar
- **Login OAuth:** https://houston1.oldlinecyber.com/auth/google/callback
- **Calendar Callback:** https://houston1.oldlinecyber.com/admin/calendar/callback

## ⚙️ Required .env Variables (VPS)

```env
GOOGLE_CLIENT_ID=your-actual-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-actual-client-secret
GOOGLE_REDIRECT_URI=https://houston1.oldlinecyber.com/auth/google/callback
GOOGLE_CALENDAR_REDIRECT_URI=https://houston1.oldlinecyber.com/admin/calendar/callback
```

## 🔧 Quick Commands

### Check Connection Status
```bash
cd /var/www/smbgen
php artisan calendar:diagnose
# OR
bash deployment/check-google-connection.sh
```

### View Recent Logs
```bash
tail -100 /var/www/smbgen/storage/logs/laravel.log | grep -i google
```

### Clear Cache After Config Changes
```bash
cd /var/www/smbgen
php artisan config:clear
php artisan cache:clear
```

### Test Token Refresh
```bash
php artisan tinker
```
```php
$user = User::find(1);
$user->googleCredential->refreshAccessToken();
```

## 🐛 Common Issues & Fixes

### Issue: "400 Bad Request" Error

**Cause:** Wrong redirect URI or missing authorization code

**Fix:**
1. Verify `GOOGLE_CALENDAR_REDIRECT_URI` is set in `.env`
2. Add `/admin/calendar/callback` to Google Console
3. Run `php artisan config:clear`

### Issue: No Refresh Token

**Cause:** User already authorized, Google won't send token again

**Fix:**
1. Go to: https://myaccount.google.com/permissions
2. Remove access to your app
3. Reconnect at `/admin/calendar`

### Issue: Token Expired

**Fix:** Should auto-refresh. If not:
```bash
php artisan tinker
```
```php
$user = User::find(1);
$user->googleCredential->delete();
```
Then reconnect at `/admin/calendar`

## 📋 Google Console Required Settings

### Authorized Redirect URIs (BOTH required)
```
https://houston1.oldlinecyber.com/auth/google/callback
https://houston1.oldlinecyber.com/admin/calendar/callback
```

### Required Scopes
```
openid
profile  
email
https://www.googleapis.com/auth/calendar.events
https://www.googleapis.com/auth/calendar.readonly
https://www.googleapis.com/auth/drive.file
```

### Required APIs (Enable in Console)
- ✅ Google Calendar API
- ✅ Google People API  
- ✅ Google Drive API

## 🎯 The Two OAuth Flows

| | Login OAuth | Calendar OAuth |
|---|---|---|
| **URL** | `/auth/google` | `/admin/calendar` |
| **Callback** | `/auth/google/callback` | `/admin/calendar/callback` |
| **Scopes** | profile, email | + calendar, drive |
| **Purpose** | Sign in | Sync events |
| **Refresh Token** | No | **YES (required)** |

## ✅ Successful Connection Looks Like

### In Logs
```
production.INFO: Google Calendar callback received
production.INFO: Google user retrieved {"has_refresh_token":true}
production.INFO: Google credentials saved successfully
```

### In Database
```php
$cred = GoogleCredential::first();
// Has: access_token, refresh_token, expires_at, calendar_id
```

### In UI
- Green success message: "Google Calendar connected successfully!"
- Can create bookings with Meet links
- Events sync to Google Calendar

## 📞 When All Else Fails

1. Run diagnostic: `php artisan calendar:diagnose`
2. Check logs: `tail -100 storage/logs/laravel.log | grep google`
3. Verify .env has all 4 GOOGLE_* variables
4. Verify Google Console has both redirect URIs
5. Verify Calendar API is enabled in Google Console
6. Try revoking and reconnecting

---

**Remember:** Use `/admin/calendar` for calendar connection, NOT `/auth/google`!
