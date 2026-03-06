# Laravel Project Setup - Issues & Solutions

## Overview
This document summarizes the issues encountered while setting up the Laravel project on WSL 2 and their solutions.

---

## Issue 1: Missing PHP Extensions (DOM & XML)

**Error:**
```
tijsverkoyen/css-to-inline-styles requires ext-dom *
laravel/pint requires ext-xml *
```

**Solution:**
```bash
sudo apt-get update
sudo apt-get install php-xml php-dom

# Verify installation
php -m | grep -E 'dom|xml'

# Then run
composer install
```

---

## Issue 2: Node.js Version Too Old

**Error:**
```
TypeError: crypto.hash is not a function
```

**Cause:** Node.js version doesn't support `crypto.hash()` (requires Node.js v18.11.0+)

**Solution:**
```bash
# Using NVM (recommended)
nvm install --lts
nvm use --lts
nvm alias default node

# Clean and reinstall
rm -rf node_modules package-lock.json
npm install
npm run dev
```

---

## Issue 3: Vite Proxy Connection Refused

**Error:**
```
http proxy error: /
Error: connect ECONNREFUSED 127.0.0.1:80
```

**Cause:** Laravel server not running or `APP_URL` mismatch

**Solution:**

1. **Start Laravel server:**
   ```bash
   php artisan serve
   ```

2. **Update `.env` to match Laravel's port:**
   ```env
   APP_URL=http://localhost:8000
   ```

3. **Run both servers:**
   - Terminal 1: `php artisan serve`
   - Terminal 2: `npm run dev`
   - Access at: `http://localhost:8000`

---

## Issue 4: Git Configuration on WSL 2

**Setup Steps:**
```bash
# Install Git
sudo apt install git -y

# Configure identity
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Generate SSH key
ssh-keygen -t ed25519 -C "your.email@example.com"
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519

# Copy public key and add to GitHub/GitLab
cat ~/.ssh/id_ed25519.pub
```

**Change remote from HTTPS to SSH:**
```bash
# Check current remote
git remote -v

# Change to SSH
git remote set-url origin git@github.com:username/repository.git

# Verify
git remote -v
```

---

## Issue 5: SQLite Driver Missing

**Error:**
```
could not find driver (Connection: sqlite)
```

**Solution:**
```bash
# Install SQLite extension
sudo apt-get install php-sqlite3

# Verify
php -m | grep sqlite

# Create database file
touch database/database.sqlite

# Run migrations
php artisan migrate:fresh --seed
```

**Alternative: Switch to MySQL**
```env
# In .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

## Complete Setup Checklist

- [ ] Install PHP extensions: `php-xml`, `php-dom`, `php-sqlite3`
- [ ] Update Node.js to v18.11.0+ (use NVM)
- [ ] Configure Git with SSH keys
- [ ] Update `.env` with correct `APP_URL`
- [ ] Run `composer install`
- [ ] Run `npm install`
- [ ] Create database (SQLite file or MySQL/PostgreSQL)
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate:fresh --seed`
- [ ] Start Laravel: `php artisan serve`
- [ ] Start Vite: `npm run dev`
- [ ] Access application at `http://localhost:8000`

---

## Useful Commands Reference

```bash
# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Check PHP modules
php -m

# Check Node/NPM versions
node -v
npm -v

# Pull latest changes
git pull origin main

# Update dependencies
composer install
npm install
```

---

## Notes

- Always run Laravel and Vite in separate terminal windows
- WSL 2 files are at `/mnt/c/Users/YourUsername/` for Windows access
- Use `http://localhost:8000` not `http://localhost:5173` to access the app
- SSH keys are stored in `~/.ssh/` directory