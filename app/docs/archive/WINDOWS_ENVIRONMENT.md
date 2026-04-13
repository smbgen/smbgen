# Windows Environment Setup & Troubleshooting

This document covers Windows-specific configuration and common issues when developing SMBGen on Windows with Laravel Herd and Git Bash.

## Table of Contents
- [Laravel Herd Configuration](#laravel-herd-configuration)
- [Git Bash Compatibility](#git-bash-compatibility)
- [Common Issues](#common-issues)
- [Environment Setup](#environment-setup)

## Laravel Herd Configuration

### Overview
Laravel Herd is a native Windows application that provides PHP, Composer, and related tools. It installs in the user's home directory under `.config/herd/`.

**Installation Location:**
```
C:\Users\<username>\.config\herd\
```

**Key Directories:**
- **PHP Binaries:** `~/.config/herd/bin/php84/` (version number may vary)
- **Herd Executables:** `~/.config/herd/bin/`
- **Composer:** `~/.config/herd/bin/composer.bat`

### Included Tools
Herd provides the following tools as Windows batch files (`.bat`):
- `php.bat` - PHP CLI
- `composer.bat` - Composer dependency manager
- `laravel.bat` - Laravel installer
- `herd.bat` - Herd management CLI

## Git Bash Compatibility

### The Problem
Git Bash on Windows cannot execute `.bat` (batch) files directly through standard shell aliases. This causes issues when trying to use Herd-provided tools like Composer.

**Symptom:**
```bash
$ composer --version
bash: composer: command not found
```

### The Solution

Create a `~/.bashrc` file with bash functions that wrap the batch files using `cmd //c`:

**File Location:** `C:\Users\<username>\.bashrc`

**Contents:**
```bash
# Laravel Herd Aliases - please do not remove these lines
alias php="php.bat"
alias herd="herd.bat"
alias laravel="laravel.bat"

# Composer function for Git Bash compatibility
composer() {
    cmd //c "C:\Users\alexr\.config\herd\bin\composer.bat $*"
}
export -f composer
```

**Why This Works:**
1. Simple aliases work for PHP, Herd, and Laravel because they're in the system PATH
2. Composer requires a function because we need to pass arguments (`$*`) to the batch file
3. `cmd //c` executes a Windows command and returns to bash
4. The double slash `//` prevents Git Bash from converting to a Unix path
5. `export -f composer` makes the function available to subshells

### Making It Permanent

To load `.bashrc` automatically on every Git Bash session, create a `~/.bash_profile`:

**File Location:** `C:\Users\<username>\.bash_profile`

**Contents:**
```bash
# Load .bashrc if it exists
if [ -f ~/.bashrc ]; then
    source ~/.bashrc
fi
```

### Manual Loading

If you don't want to create `.bash_profile`, you can manually load the configuration in each session:

```bash
source ~/.bashrc
```

## Common Issues

### Issue: "Target class [auth] does not exist" After Git Pull

**Symptom:** Running `php artisan` commands fails with "Target class [auth] does not exist"

**Root Cause:** The `bootstrap/app.php` file contains an unguarded call to `auth()` in the exceptions context that executes before Laravel's authentication system is fully bootstrapped during CLI commands. This often occurs after git pulls when changes revert previous fixes.

**Solution:** Guard the `auth()` call in `bootstrap/app.php`

```php
// ❌ PROBLEMATIC CODE (causes CLI failures)
$exceptions->context(fn () => [
    'user_id' => auth()->id() ?? null,
    'env' => app()->environment(),
]);

// ✅ FIXED CODE (safe for CLI)
$exceptions->context(fn () => [
    'user_id' => (function () {
        try {
            return function_exists('auth') && app()->bound('auth') ? auth()->id() : null;
        } catch (\Throwable $e) {
            return null;
        }
    })(),
    'env' => app()->environment(),
]);
```

**Prevention:** Always guard early auth calls in bootstrap files with existence checks and try/catch blocks.

### Issue: "Class 'Laravel\Boost\BoostServiceProvider' not found"

**Symptom:** Artisan commands fail with provider class not found errors after git pull or dependency changes.

**Root Cause:** Composer autoload mappings are stale, or Laravel's package discovery cache is outdated.

**Solution Steps:**

1. **Create Required Directories** (if missing):
```bash
mkdir -p storage/framework/views storage/logs bootstrap/cache
```

2. **Regenerate Autoload Files**:
```bash
composer dump-autoload --optimize --no-scripts
```

3. **Clear Laravel Caches**:
```bash
php artisan optimize:clear
```

4. **Regenerate Package Discovery**:
```bash
php artisan package:discover --ansi
```

5. **Verify Provider Discovery**:
```bash
php artisan tinker --execute="var_export(class_exists('Laravel\\Boost\\BoostServiceProvider'));"
```

### Issue: Composer Script Failures During Install

**Symptom:** `composer install` or `composer dump-autoload` fails with "pre-autoload-dump event returned with error code 1"

**Root Cause:** Composer scripts try to create directories that don't exist or have permission issues during the pre-autoload-dump phase.

**Solution:** 
```bash
# Create directories manually first (prevents script failure)
mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

# Then run composer install normally (script will now succeed)
composer.bat install --optimize-autoloader

# Alternative if still failing - bypass scripts entirely:
composer dump-autoload --optimize --no-scripts
php artisan optimize:clear && php artisan package:discover
```

### Issue: MissingAppKeyException After Environment Changes

**Symptom:** `MissingAppKeyException` errors in tests or application

**Root Cause:** The `APP_KEY` in `.env` is missing or invalid after environment changes or fresh pulls.

**Solution:**
```bash
php artisan key:generate
```

**Verification:**
```bash
php artisan config:show app.key
# Should show: app.key .... base64:FBmJhkH6BD0AZ2UWtRosyN4Eaa372d2yuBUP79At1VU=
```

### Issue: Login Redirects to Wrong URLs (e.g., /admin/leads/partial)

**Symptom:** After login, users are redirected to AJAX endpoints or partial views instead of proper dashboards.

**Root Cause:** Laravel's `redirect()->intended()` method stores the last attempted URL in the session. If users previously accessed partial views or AJAX endpoints, the login redirect uses those URLs.

**Solution:** Clear intended URLs in the AuthenticatedSessionController:
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Clear any intended URL to prevent unwanted redirects
    $request->session()->forget('url.intended');

    // Force direct redirect based on user role
    $user = auth()->user();
    if ($user->role === 'company_administrator') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('dashboard');
    }
}
```

**Session Debugging:**
```bash
# Clear all sessions to eliminate stored URLs
php artisan tinker --execute="DB::table('sessions')->truncate();"

# Check session count
php artisan tinker --execute="echo 'Session count: ' . DB::table('sessions')->count();"
```

### Issue: PSR-4 Autoloading Standard Violations

**Symptom:** "Class [Name] does not comply with psr-4 autoloading standard" during composer operations.

**Example Fix:**
```php
// ❌ Missing namespace declaration
<?php
use Tests\TestCase;
class EmailDeliverabilityTest extends TestCase

// ✅ Proper namespace declaration
<?php
namespace Tests\Feature;
use Tests\TestCase;
class EmailDeliverabilityTest extends TestCase
```

### Issue: "composer: command not found"

**Cause:** Git Bash cannot execute `.bat` files directly through aliases.

**Solution:** Use the bash function approach documented above.

**Verification:**
```bash
$ composer --version
Composer version 2.8.8 2025-04-04 16:56:46
PHP version 8.4.7 (C:\Users\alexr\.config\herd\bin\php84\php.exe)
```

### Issue: "php: command not found"

**Cause:** Herd's PHP bin directory is not in your PATH.

**Solution:** 
1. Check if Herd is running (system tray icon)
2. Restart Herd application
3. Close and reopen Git Bash
4. Verify PATH includes: `~/.config/herd/bin/php84/`

**Check PATH:**
```bash
echo $PATH | grep -o ".config/herd/bin/php84"
```

### Issue: Laravel commands fail with "Class not found"

**Cause:** Autoloader is out of sync or dependencies are missing.

**Solution:**
```bash
composer install
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue: CRLF vs LF line endings

**Symptom:** Git warnings about CRLF/LF conversions, scripts fail to execute.

**Solution:** Configure Git to handle line endings properly:

```bash
# For this repository only
git config core.autocrlf true

# Globally for all repositories
git config --global core.autocrlf true
```

**Alternative:** Use `.gitattributes` file (already in project):
```
* text=auto eol=lf
*.bat text eol=crlf
```

### Issue: Permissions errors on storage/logs

**Cause:** Windows file permissions differ from Unix.

**Solution:**
```bash
# These commands work in Git Bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Or use PHP to set permissions
php artisan storage:link
```

### Issue: Symlinks not working

**Cause:** Windows requires administrator privileges to create symlinks.

**Solution:**
1. Run Git Bash as Administrator
2. Or enable Developer Mode in Windows Settings
3. Then run: `php artisan storage:link`

## Environment Setup

### Required Environment Variables

The following are set automatically by Herd but can be verified:

```bash
# Check PHP version
php --version

# Check Composer version
composer --version

# Check Laravel installer
laravel --version

# Verify PATH includes Herd
echo $PATH | grep herd
```

### Recommended Git Bash Configuration

**`~/.bashrc`:**
```bash
# Laravel Herd Aliases
alias php="php.bat"
alias herd="herd.bat"
alias laravel="laravel.bat"

# Composer function for Git Bash compatibility
composer() {
    cmd //c "C:\Users\alexr\.config\herd\bin\composer.bat $*"
}
export -f composer

# Laravel Artisan alias
alias artisan="php artisan"

# Common shortcuts
alias tinker="php artisan tinker"
alias migrate="php artisan migrate"
alias fresh="php artisan migrate:fresh --seed"

# Git shortcuts
alias gs="git status"
alias gp="git push"
alias gl="git log --oneline -10"
```

### VSCode Integration

If using VSCode with Git Bash as the integrated terminal:

**settings.json:**
```json
{
    "terminal.integrated.defaultProfile.windows": "Git Bash",
    "terminal.integrated.profiles.windows": {
        "Git Bash": {
            "path": "C:\\Program Files\\Git\\bin\\bash.exe",
            "args": ["-l"]
        }
    }
}
```

The `-l` flag ensures it loads as a login shell, which sources `~/.bash_profile` and `~/.bashrc`.

## Development Workflow

### Starting Development

1. **Ensure Herd is running** (check system tray)
2. **Open Git Bash** in project directory
3. **Load environment** (if not auto-loaded):
   ```bash
   source ~/.bashrc
   ```
4. **Verify tools are available:**
   ```bash
   php --version
   composer --version
   ```
5. **Start development:**
   ```bash
   php artisan serve
   # Or use Herd's built-in server (automatic)
   ```

### Deployment to VPS (Ubuntu/Linux)

When deploying from Windows to Linux VPS, be aware of:

1. **Line endings:** Ensure `.gitattributes` is properly configured
2. **Path differences:** Windows uses backslashes, Linux uses forward slashes
3. **Case sensitivity:** Linux filesystems are case-sensitive
4. **Permissions:** Linux requires specific file permissions (775 for storage, 644 for files)
5. **Batch files:** `.bat` files won't work on Linux (use bash scripts instead)

### Testing

```bash
# Run tests
php artisan test

# Or with Pest
./vendor/bin/pest

# Run specific test
php artisan test --filter=BookingTest
```

## Quick Recovery Checklist After Git Pull

When encountering issues after `git pull` or major environment changes, follow this systematic approach:

### 1. Verify Environment Basics
```bash
# Check PHP availability
php -v

# Check Composer availability  
composer -V

# Verify you're in the project root
pwd
# Should show: /c/Users/[username]/Documents/GitHub/smbgen
```

### 2. Fix Bootstrap Authentication Issues
```bash
# Test basic artisan functionality
php artisan --version

# If you get "Target class [auth] does not exist":
# Check bootstrap/app.php for unguarded auth() calls in exceptions context
# Apply the guarded auth solution documented above
```

### 3. Regenerate Dependencies & Caches
```bash
# Create required directories (if missing)
mkdir -p storage/framework/views storage/logs bootstrap/cache

# Regenerate autoload (bypass scripts if they fail)
composer dump-autoload --optimize --no-scripts

# Clear and regenerate ALL Laravel caches
php artisan optimize:clear

# Regenerate package discovery cache
php artisan package:discover --ansi
```

### 4. Handle Database & Application Keys
```bash
# Generate app key if missing (prevents MissingAppKeyException)
php artisan key:generate

# Run any pending migrations
php artisan migrate

# Clear sessions if experiencing login redirect issues
php artisan tinker --execute="DB::table('sessions')->truncate();"
```

### 5. Build Frontend Assets
```bash
# Install/update NPM dependencies
npm install

# Build production assets
npm run build
```

### 6. Reinstall Development Tools
```bash
# Reinstall Laravel Boost MCP server for VS Code integration
php artisan boost:install
```

### 7. Final Verification Tests
```bash
# Test artisan commands work
php artisan route:list | head -5

# Test class autoloading
php artisan tinker --execute="var_export(class_exists('Laravel\\Boost\\BoostServiceProvider'));"

# Test application key
php artisan config:show app.key

# Test basic authentication (if logged in)
php artisan tinker --execute="echo auth()->check() ? 'Auth working' : 'Auth ready';"
```

## Standard Troubleshooting Checklist

For general development issues:

- [ ] Is Herd running? (Check system tray)
- [ ] Did you source `~/.bashrc`? Run: `source ~/.bashrc`
- [ ] Is PHP in PATH? Run: `which php.bat`
- [ ] Is Composer working? Run: `composer --version`
- [ ] Are dependencies installed? Run: `composer install`
- [ ] Is `.env` file present? Run: `ls -la .env`
- [ ] Are permissions correct? Run: `ls -la storage/`
- [ ] Bootstrap issues? Check: `bootstrap/app.php` for unguarded auth calls
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Regenerate discovery: `php artisan package:discover`
- [ ] Restart Herd application
- [ ] Close and reopen Git Bash

## Emergency Recovery Commands

**Full environment reset after major issues:**
```bash
# Complete recovery sequence (run in project root)
mkdir -p storage/framework/views storage/logs bootstrap/cache
composer dump-autoload --optimize --no-scripts
php artisan optimize:clear
php artisan package:discover --ansi
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan boost:install
```

**Debug specific issues:**
```bash
# Check authentication system
php artisan tinker --execute="var_export([
    'auth_function_exists' => function_exists('auth'),
    'auth_bound' => app()->bound('auth'),
    'user_model_exists' => class_exists('App\\Models\\User')
]);"

# Verify critical service providers are discovered
php artisan tinker --execute="var_export([
    'boost_provider' => class_exists('Laravel\\Boost\\BoostServiceProvider'),
    'mcp_provider' => class_exists('Laravel\\Mcp\\Server\\McpServiceProvider'),
    'breeze_provider' => class_exists('Laravel\\Breeze\\BreezeServiceProvider')
]);"

# Clear all caches and sessions completely
php artisan optimize:clear && php artisan tinker --execute="
    DB::table('sessions')->truncate();
    echo 'All caches and sessions cleared.';
"
```

## Additional Resources

- **Laravel Herd Documentation:** https://herd.laravel.com/
- **Git Bash Documentation:** https://git-scm.com/docs
- **Laravel Documentation:** https://laravel.com/docs
- **Composer Documentation:** https://getcomposer.org/doc/

## Maintenance

### Updating Herd

Herd auto-updates, but you can manually check:
1. Click Herd icon in system tray
2. Select "Check for Updates"
3. Restart Herd after update
4. Verify PHP/Composer versions: `php --version && composer --version`

### Updating Composer

```bash
composer self-update
```

### Changing PHP Version

Herd supports multiple PHP versions:
1. Right-click Herd icon in system tray
2. Select PHP version (8.1, 8.2, 8.3, 8.4)
3. Update `.bashrc` path if needed: `~/.config/herd/bin/php84/` → `php83/` etc.

## Prevention Best Practices

### Before Git Pull
- [ ] Commit or stash local changes
- [ ] Note any custom environment configurations
- [ ] Backup `.env` file if it contains unique local settings

### After Git Pull
- [ ] Run the Quick Recovery Checklist above
- [ ] Check `bootstrap/app.php` for reverted auth guards
- [ ] Verify critical routes still work: `php artisan route:list | grep dashboard`
- [ ] Test login functionality to ensure proper redirects

### Code Practices
1. **Always guard early auth calls** in bootstrap contexts:
   ```php
   // Safe pattern for bootstrap files
   'user_id' => (function () {
       try {
           return function_exists('auth') && app()->bound('auth') ? auth()->id() : null;
       } catch (\Throwable $e) {
           return null;
       }
   })(),
   ```

2. **Clear intended URLs** in login controllers:
   ```php
   // Prevent unwanted redirects after authentication
   $request->session()->forget('url.intended');
   ```

3. **Use proper namespaces** in test files:
   ```php
   <?php
   namespace Tests\Feature; // Always include namespace
   ```

4. **Regenerate caches** after dependency changes:
   ```bash
   composer dump-autoload --optimize
   php artisan optimize:clear
   php artisan package:discover
   ```

## Script Automation

### Setup Script for New Windows Environments

Create `scripts/setup-herd-gitbash.sh` (already exists in project):

```bash
#!/bin/bash
# Sets up Git Bash to work with Laravel Herd on Windows

BASHRC="$HOME/.bashrc"
HERD_BIN="$HOME/.config/herd/bin"

# Add PATH export for Herd binaries
if ! grep -q "export PATH.*herd/bin" "$BASHRC" 2>/dev/null; then
    echo "" >> "$BASHRC"
    echo "# Laravel Herd PATH" >> "$BASHRC"
    echo "export PATH=\"\$HOME/.config/herd/bin:\$PATH\"" >> "$BASHRC"
    echo "Added Herd bin to PATH"
else
    echo "Herd PATH already configured"
fi

# Add aliases for .bat files
ALIASES=(
    "alias php='php.bat'"
    "alias composer='composer.bat'"
    "alias herd='herd.bat'"
    "alias laravel='laravel.bat'"
)

for alias_cmd in "${ALIASES[@]}"; do
    if ! grep -Fq "$alias_cmd" "$BASHRC" 2>/dev/null; then
        echo "$alias_cmd" >> "$BASHRC"
        echo "Added: $alias_cmd"
    else
        echo "Alias already exists: $alias_cmd"
    fi
done

echo "Setup complete! Run 'source ~/.bashrc' to apply changes."
```

### Post-Pull Recovery Script

Consider creating `scripts/post-pull-recovery.sh`:

```bash
#!/bin/bash
# Automated recovery after git pull

echo "🔧 Starting post-pull recovery..."

# Step 1: Create required directories
echo "📁 Creating required directories..."
mkdir -p storage/framework/views storage/logs bootstrap/cache

# Step 2: Fix autoload issues
echo "🔄 Regenerating autoload..."
composer dump-autoload --optimize --no-scripts

# Step 3: Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan optimize:clear

# Step 4: Regenerate package discovery
echo "📦 Regenerating package discovery..."
php artisan package:discover --ansi

# Step 5: Generate app key if missing
echo "🔑 Checking application key..."
if ! php artisan config:show app.key | grep -q "base64:"; then
    echo "Generating new application key..."
    php artisan key:generate
fi

# Step 6: Run migrations
echo "📊 Running migrations..."
php artisan migrate --force

# Step 7: Clear sessions
echo "🧼 Clearing sessions..."
php artisan tinker --execute="DB::table('sessions')->truncate(); echo 'Sessions cleared';"

# Step 8: Build frontend
echo "🎨 Building frontend assets..."
npm install
npm run build

echo "echo "✅ Recovery complete! Environment should now be ready."
```

---

## Windows-Specific Directory Permission Issues

### Issue: "bootstrap\cache directory must be present and writable"

**Symptom:** Laravel fails with error: "The bootstrap\cache directory must be present and writable."

**Root Cause:** Windows file system permissions or read-only attributes prevent Laravel from writing to the bootstrap/cache directory.

**Solutions (in order of preference):**

**Solution 1 - Remove Read-Only Attribute (RECOMMENDED):**
```powershell
# In PowerShell (no admin required)
attrib -r +a .\bootstrap\cache
```

**Solution 2 - Create Directory with Proper Attributes:**
```powershell
# In PowerShell
New-Item -Path "bootstrap\cache" -ItemType Directory -Force
attrib -r +a .\bootstrap\cache
```

**Solution 3 - Grant Full Permissions:**
```powershell
# In PowerShell as Administrator
icacls "bootstrap\cache" /grant Everyone:F /T
```

**Solution 4 - Use the Fix Script:**
```bash
# In Git Bash
bash scripts/fix-bootstrap-cache.sh
```

**Verification:**
```bash
# Test write permissions
touch bootstrap/cache/test.tmp
rm bootstrap/cache/test.tmp
```

**Notes:**
- The `attrib` command removes the read-only flag (`-r`) and sets the archive attribute (`+a`)
- This is often needed after cloning a repository on Windows
- The issue can also occur after extracting a ZIP archive
- Antivirus software may sometimes interfere with directory creation

---

**Last Updated:** October 17, 2025  
````"
```

---

**Last Updated:** October 12, 2025  
**Issues Documented:** Target class [auth] errors, Provider not found errors, Login redirect issues, Composer script failures, PSR-4 violations, MissingAppKeyException  
**Maintainer:** Alexander Ramsey (@alexramsey92)  
**Related Docs:** [DEPLOYMENT.md](./DEPLOYMENT.md), [ASSISTANT_GUIDE.md](./ASSISTANT_GUIDE.md), [BOOST_MCP_SERVER.md](./BOOST_MCP_SERVER.md)
