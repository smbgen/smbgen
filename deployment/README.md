# Deployment Scripts

This folder contains all VPS deployment and debugging scripts for smbgen.

## 📦 Deployment Scripts

### `vps-deploy.sh` - Main Deployment Script
Complete deployment automation for VPS.

**Usage:**
```bash
ssh root@houston1.oldlinecyber.com
cd /home/alex/smbgen
bash deployment/vps-deploy.sh
```

**What it does:**
- Pulls latest code from git
- Installs composer dependencies (production mode)
- Runs database migrations
- Clears all caches
- Rebuilds optimized caches
- Sets proper permissions
- Builds frontend assets (if npm available)
- Restarts PHP-FPM and Nginx

---

## 🔧 Quick Fix Scripts

### `vps-quickfix.sh`
Quick cache clear and permission fix for common issues.

### `fix-migrations.sh`
Fixes migration issues on VPS.

### `fix-vps-user-model.sh`
Updates User model and clears caches.

---

## 🐛 Debug Scripts

### `debug-vps.sh`
Comprehensive VPS diagnostics - checks PHP, database, permissions, logs, etc.

**Usage:**
```bash
ssh root@houston1.oldlinecyber.com
cd /home/alex/smbgen
bash deployment/debug-vps.sh
```

### `debug-booking-error.sh`
Specific debugging for booking system errors.

### `check-vps-logs.sh`
Quick log viewer for Laravel and Nginx logs.

---

## 🌐 Network Scripts

### `whitelist-my-ip.sh`
Auto-detects your IP and whitelists it in Nginx config.

**Your IP:** 68.33.53.17

**Usage:**
```bash
bash deployment/whitelist-my-ip.sh
```

### `vps-whitelist-instructions.txt`
Manual instructions for IP whitelisting.

---

## 🚀 Common Deployment Workflow

1. **Develop locally** - Make changes, test on Herd
2. **Commit and push** - `git add -A && git commit -m "..." && git push`
3. **SSH to VPS** - `ssh root@houston1.oldlinecyber.com`
4. **Navigate** - `cd /home/alex/smbgen`
5. **Deploy** - `bash deployment/vps-deploy.sh`
6. **Check logs** - `tail -f storage/logs/laravel.log`

---

## ⚠️ VPS Configuration

**Server:** houston1.oldlinecyber.com  
**User:** root / alex  
**App Path:** /home/alex/smbgen  
**PHP Version:** 8.4  
**Web Server:** Nginx + PHP-FPM  
**Database:** SQLite  
**Git Remote:** github.com/alexramsey92/smbgen

---

## 🔒 Security Notes

- IP whitelisting active on admin routes
- Rate limiting: 5 requests/min admin, 1 request/min login
- HSTS headers enabled
- SSL certificates via Let's Encrypt

---

## 📋 Troubleshooting

**500 Errors:**
1. Run `debug-vps.sh` to diagnose
2. Check logs: `tail -f storage/logs/laravel.log`
3. Clear caches: `php artisan cache:clear && php artisan config:clear`
4. Check permissions: `ls -la storage/`

**Route Not Found:**
1. Clear route cache: `php artisan route:clear`
2. Rebuild cache: `php artisan route:cache`

**Vite Manifest Missing:**
1. Install Node.js 24.5: `curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -`
2. **CRITICAL for Windows development:** Node.js 24.5 required for npm install
3. Build assets: `npm install && npm run build`

**Windows Development npm install Issues:**
1. Use Node.js 24.5: `nvm install 24.5 && nvm alias default 24.5`
2. Run setup script: `bash scripts/setup-nodejs-24-5-windows.sh`
3. Newer Node.js versions (25+) break npm install on Windows as of October 2025
