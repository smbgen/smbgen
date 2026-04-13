# VPS Debugging Fiasco - The Complete Saga

## 🚨 The Problem
**Initial Issue**: Navbar disappeared on VPS but worked locally. Frontend was "all jacked up" and not responsive.

## 🔍 Root Cause Analysis

### The Cascade of Issues:
1. **Node.js Version Mismatch**: VPS had Node.js v18, but Vite v7 requires Node.js v20+
2. **Missing Vite Assets**: `@vite()` directive couldn't find compiled assets
3. **Nginx Configuration**: Was serving directory listings instead of Laravel
4. **Missing index.php**: Entire `public/` directory was accidentally deleted
5. **Database Permissions**: SQLite database was read-only

## 📋 Timeline of Events

### Phase 1: Responsive Design Issues
- **Problem**: Frontend not responsive on VPS
- **Diagnosis**: Missing viewport meta tag and Vite assets not loading
- **Fixes Applied**:
  - Added `<meta name="viewport" content="width=device-width, initial-scale=1">`
  - Added `@vite(['resources/css/app.css', 'resources/js/app.js'])` to layouts
  - Updated Bootstrap versions to be consistent (5.3.3)
  - Improved responsive grid system in dashboard
  - Added mobile-friendly table layouts

### Phase 2: Node.js Version Hell
- **Problem**: `npm WARN EBADENGINE Unsupported engine` errors
- **Root Cause**: VPS had Node.js v18.19.1, but Vite v7 requires Node.js v20+
- **Solution**: Upgraded to Node.js v24 using nvm
- **Commands**:
  ```bash
  curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
  source ~/.bashrc
  nvm install 24
  nvm use 24
  nvm alias default 24
  ```

### Phase 3: The Great Public Directory Disaster
- **Problem**: `ls -la /home/alex/smbgen/public/index.php` → "no such file or directory"
- **Root Cause**: User ran `rm -rf public` and `git pull` didn't restore it
- **Why Git Didn't Help**: 
  - `public/index.php` IS tracked in git
  - `public/build/` is in `.gitignore` (correctly)
  - Git restore process failed due to permission issues
- **Solution**: `git checkout HEAD -- public/`

### Phase 4: Nginx Configuration Chaos
- **Problem**: 403 Forbidden errors with "directory index forbidden"
- **Root Cause**: Nginx was trying to serve directory listings instead of Laravel
- **Error Logs**:
  ```
  directory index of "/home/alex/smbgen/public/" is forbidden
  ```
- **Solution**: Proper Laravel Nginx configuration with `try_files` directive

### Phase 5: Database Permission Nightmare
- **Problem**: `SQLSTATE[HY000]: General error: 8 attempt to write a readonly database`
- **Root Cause**: SQLite database file had wrong permissions
- **Solution**: 
  ```bash
  sudo chown www-data:www-data /home/alex/smbgen/database/database.sqlite
  sudo chmod 664 /home/alex/smbgen/database/database.sqlite
  ```

## 🛠️ The Complete Fix Sequence

### 1. Node.js Upgrade
```bash
# Install nvm and upgrade Node.js
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 24
nvm use 24
nvm alias default 24
```

### 2. Restore Missing Files
```bash
# Restore public directory from git
git checkout HEAD -- public/

# Set proper ownership
sudo chown -R alex:alex ~/smbgen
sudo chown -R www-data:www-data ~/smbgen/storage
sudo chown -R www-data:www-data ~/smbgen/bootstrap/cache
sudo chown -R www-data:www-data ~/smbgen/public
```

### 3. Rebuild Assets
```bash
# Clean and rebuild
rm -rf node_modules package-lock.json public/build
npm install
npm run build
```

### 4. Fix Database Permissions
```bash
# Fix SQLite permissions
sudo chown www-data:www-data /home/alex/smbgen/database/database.sqlite
sudo chmod 664 /home/alex/smbgen/database/database.sqlite
sudo chown www-data:www-data /home/alex/smbgen/database/
sudo chmod 775 /home/alex/smbgen/database/
```

### 5. Verify Nginx Configuration
```nginx
server {
    listen 443 ssl;
    server_name houston1.oldlinecyber.com;
    
    root /home/alex/smbgen/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🎯 Key Lessons Learned

### 1. **Never Use `sudo npm install`**
- Causes permission issues
- Install as regular user, fix ownership separately

### 2. **Node.js Version Compatibility**
- Always check Node.js version requirements for build tools
- Vite v7+ requires Node.js v20+
- Use nvm for version management

### 3. **Git and .gitignore Understanding**
- `public/index.php` is tracked in git
- `public/build/` is ignored (correctly)
- `git pull` doesn't always restore deleted directories properly

### 4. **Laravel File Permissions**
- `alex` owns application code
- `www-data` owns `storage/`, `bootstrap/cache/`, `public/`
- Database files need write permissions for `www-data`

### 5. **Nginx Laravel Configuration**
- Must have `try_files $uri $uri/ /index.php?$query_string;`
- Document root must point to `public/` directory
- PHP-FPM socket must be correct

### 6. **Debugging Strategy**
- Check error logs first (Nginx, Laravel)
- Verify file existence and permissions
- Test each component separately
- Don't assume one fix solves everything

## 🚀 Final Status
- ✅ Responsive design working
- ✅ Vite assets building properly
- ✅ Nginx serving Laravel correctly
- ✅ Database permissions fixed
- ✅ Google OAuth working
- ✅ All user accounts accessible

## 📝 Commands to Remember

```bash
# Check what's wrong
sudo tail -f /var/log/nginx/error.log
tail -f /home/alex/smbgen/storage/logs/laravel.log

# Fix permissions
sudo chown -R alex:alex ~/smbgen
sudo chown -R www-data:www-data ~/smbgen/storage ~/smbgen/bootstrap/cache ~/smbgen/public

# Rebuild everything
rm -rf node_modules package-lock.json public/build
npm install
npm run build

# Test Nginx config
sudo nginx -t
sudo systemctl reload nginx
```

## 🎉 Moral of the Story
**One small change (Node.js upgrade) can cascade into multiple issues. Always check dependencies, permissions, and configurations systematically.**
