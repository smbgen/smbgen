# Google Calendar Fix - Deployment Checklist

## Critical Fix Deployed: Nov 8, 2025

### What Was Broken
The Google Calendar was not saving connections in **any environment** (VPS, cloud, local) because the `encrypted` cast on tokens was causing silent save failures.

### What Was Fixed
1. Removed problematic encryption casts
2. Added comprehensive logging
3. Created diagnostic command
4. Added legacy column support for backward compatibility

---

## Deployment Steps for Each Environment

### For ALL Environments (VPS, Cloud Instances, Local Dev)

#### 1. Pull Latest Code
```bash
cd /path/to/clientbridge-laravel
git pull origin main
```

#### 2. Install Dependencies (if needed)
```bash
composer install --no-dev --optimize-autoloader
```

#### 3. Run Diagnostic
```bash
php artisan calendar:diagnose
```

Look for:
- ✓ All configuration is set
- ✓ google_credentials table exists
- ✗ 0 user connections (this is expected before reconnecting)

#### 4. Check if Migration Needed
If diagnostic shows "Users with legacy google_refresh_token", run:
```bash
php artisan migrate --force
```

This will run the migration that safely copies legacy data to the new table.

#### 5. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

#### 6. Test Calendar Connection

1. Log in as admin
2. Go to `/admin/calendar`
3. Click "Connect Google Calendar"
4. Complete OAuth flow
5. **CHECK LOGS** for success message:
   ```
   tail -f storage/logs/laravel.log | grep -i "Google credentials saved successfully"
   ```
6. Verify in admin panel that status shows "✓ Connected"
7. Run diagnostic again to confirm:
   ```bash
   php artisan calendar:diagnose
   ```
   Should now show "Users with new GoogleCredential: 1"

#### 7. Test Booking Creation
1. Create an availability rule at `/admin/availability`
2. Visit public booking page at `/book`
3. Complete a test booking
4. Check Google Calendar for the event
5. Verify Google Meet link was generated

---

## Environment-Specific Notes

### VPS (houston1.oldlinecyber.com)
```bash
# SSH into VPS
ssh user@houston1.oldlinecyber.com

# Navigate to project
cd /path/to/project

# Follow steps 1-7 above

# Restart services
sudo systemctl restart php8.4-fpm  # or your PHP-FPM service
sudo systemctl restart nginx
```

### RTS Environment Cloud Instance
```bash
# SSH into cloud instance
ssh user@rts-cloud-ip

# Follow steps 1-7 above

# Restart web server
sudo service nginx restart
# OR
sudo service apache2 restart
```

### Local Development
```bash
# Navigate to project
cd c:/Users/alexr/Herd/clientbridge-laravel

# Follow steps 1-7 above

# Laravel Herd automatically handles restarts
# But you can restart if needed via Herd UI
```

---

## Verification Checklist

After deployment, verify on EACH environment:

- [ ] Diagnostic command shows green status
- [ ] Can connect calendar successfully
- [ ] Connection saves to database (check with diagnostic)
- [ ] Can see calendar in admin panel
- [ ] Can create availability rules
- [ ] Public booking page loads
- [ ] Test booking creates Google Calendar event
- [ ] Google Meet link is generated
- [ ] No errors in logs

---

## Rollback Plan (If Needed)

If something goes wrong:

```bash
# Revert to previous version
git log --oneline  # Find commit before the fix
git checkout <previous-commit-hash>

# Or revert the specific commit
git revert ed3eba8

# Clear caches
php artisan config:clear
php artisan cache:clear

# Restart services
```

---

## Common Issues After Deployment

### Issue: Still can't save connection
**Check:**
1. APP_KEY is set in .env
2. Database connection is working
3. google_credentials table exists
4. Check logs for specific error messages

**Solution:**
```bash
php artisan key:generate  # If APP_KEY is missing
php artisan migrate --force  # Ensure tables exist
tail -100 storage/logs/laravel.log | grep -i "google\|calendar"
```

### Issue: "No refresh token received"
**This means Google isn't providing offline access.**

**Solution:**
1. Revoke access at https://myaccount.google.com/permissions
2. Reconnect calendar
3. Make sure to approve ALL requested permissions

### Issue: Tokens expire immediately
**Check expires_at in database:**
```bash
php artisan tinker
GoogleCredential::first()->expires_at
```

**Solution:**
Tokens are set to expire in 1 hour by default. The auto-refresh will handle it.

---

## Support Commands

### View all connections
```bash
php artisan tinker
\App\Models\GoogleCredential::with('user')->get()
```

### Manually refresh a token
```bash
php artisan tinker
\App\Models\User::find(1)->googleCredential->refreshAccessToken()
```

### Check logs
```bash
# Last 100 lines with calendar-related entries
tail -100 storage/logs/laravel.log | grep -i calendar

# Follow logs in real-time
tail -f storage/logs/laravel.log | grep -i "google\|calendar"
```

### Delete and reconnect (if needed)
```bash
php artisan tinker
\App\Models\GoogleCredential::where('user_id', 1)->delete()
# Then reconnect via web interface
```

---

## Success Indicators

You'll know it's working when:
1. ✅ Diagnostic shows users with GoogleCredential
2. ✅ Admin calendar page shows "Connected" status
3. ✅ Logs show "Google credentials saved successfully"
4. ✅ Can create bookings that generate calendar events
5. ✅ Google Meet links are created automatically

---

## Timeline

- **Before**: Calendar wouldn't connect on ANY environment
- **Root Cause Found**: Encrypted casts causing silent save failures
- **Fix Deployed**: Removed encryption, added logging and diagnostics
- **After**: Calendar should connect successfully on ALL environments

---

## Contact

If issues persist after following this checklist:
1. Run `php artisan calendar:diagnose` and save output
2. Check last 200 lines of laravel.log
3. Note exact error message from admin panel
4. Check browser console for JavaScript errors
