#!/usr/bin/env bash

# Setup Herd aliases for Git Bash
# This script appends safe aliases to shell profile so `php` in Git Bash will use php.bat if available.

set -euo pipefail

MARKER="# Laravel Herd aliases (smbgen)"

# Determine which profile file to use (Git Bash may use .bash_profile instead of .bashrc)
PROFILE=""
TARGET_PROFILE=""

# Git Bash on Windows prioritizes .bash_profile over .bashrc
# If .bash_profile exists, we need to use it or source .bashrc from it
if [[ -f "$HOME/.bash_profile" ]]; then
    PROFILE="$HOME/.bash_profile"
    echo "Found .bash_profile - Git Bash will use this file"
    
    # Check if .bash_profile already sources .bashrc
    if grep -q "source.*\.bashrc\|\\. .*\.bashrc" "$HOME/.bash_profile" 2>/dev/null; then
        echo ".bash_profile already sources .bashrc"
        TARGET_PROFILE="$HOME/.bashrc"
    else
        echo ".bash_profile doesn't source .bashrc - will add aliases directly"
        TARGET_PROFILE="$HOME/.bash_profile"
    fi
elif [[ -f "$HOME/.bashrc" ]]; then
    PROFILE="$HOME/.bashrc" 
    TARGET_PROFILE="$HOME/.bashrc"
    echo "Using .bashrc (no .bash_profile found)"
else
    # Create .bash_profile if neither exists (Git Bash preference)
    PROFILE="$HOME/.bash_profile"
    TARGET_PROFILE="$HOME/.bash_profile"
    touch "$PROFILE"
    echo "Created new .bash_profile"
fi

echo "Target profile for aliases: $TARGET_PROFILE"

# Check if aliases are already installed in the target profile
ALREADY_INSTALLED=false
if [[ -f "$TARGET_PROFILE" ]] && grep -qxF "$MARKER" "$TARGET_PROFILE" 2>/dev/null; then
    echo "Aliases already found in $TARGET_PROFILE"
    ALREADY_INSTALLED=true
fi

# If already installed, skip installation but still do diagnostics
if [[ "$ALREADY_INSTALLED" == "true" ]]; then
    echo "✅ Aliases already installed - running diagnostics..."
else
    echo "📝 Installing aliases to $TARGET_PROFILE..."
    cat >> "$TARGET_PROFILE" <<'BASH'

# Laravel Herd aliases (smbgen)
# Added by scripts/setup-herd-gitbash.sh
# Use php.bat, herd.bat, laravel.bat, composer.bat when php/herd/laravel/composer are missing
if ! command -v php >/dev/null 2>&1; then
    if command -v php.bat >/dev/null 2>&1; then
        alias php='php.bat'
    fi
fi

if ! command -v herd >/dev/null 2>&1; then
    if command -v herd.bat >/dev/null 2>&1; then
        alias herd='herd.bat'
    fi
fi

if ! command -v laravel >/dev/null 2>&1; then
    if command -v laravel.bat >/dev/null 2>&1; then
        alias laravel='laravel.bat'
    fi
fi

if ! command -v composer >/dev/null 2>&1; then
    if command -v composer.bat >/dev/null 2>&1; then
        alias composer='composer.bat'
    fi
fi

BASH
    echo "✅ Appended aliases to $TARGET_PROFILE"
    
    # If we used .bash_profile but .bashrc exists with aliases, also ensure .bash_profile sources .bashrc
    if [[ "$TARGET_PROFILE" == "$HOME/.bash_profile" ]] && [[ -f "$HOME/.bashrc" ]] && grep -qxF "$MARKER" "$HOME/.bashrc" 2>/dev/null; then
        if ! grep -q "source.*\.bashrc\|\\. .*\.bashrc" "$HOME/.bash_profile" 2>/dev/null; then
            echo "📝 Adding .bashrc source to .bash_profile for compatibility..."
            cat >> "$HOME/.bash_profile" <<'BASH'

# Source .bashrc for compatibility
if [ -f ~/.bashrc ]; then
    source ~/.bashrc
fi
BASH
        fi
    fi
fi

# Always reload and run diagnostics
echo "🔄 Reloading profile files..."
# shellcheck source=/dev/null
source "$PROFILE" 2>/dev/null || true
# Also try to source the target profile if different
if [[ "$TARGET_PROFILE" != "$PROFILE" ]]; then
    # shellcheck source=/dev/null
    source "$TARGET_PROFILE" 2>/dev/null || true
fi

# Run comprehensive diagnostics
echo ""
echo "🔍 Running diagnostics..."
echo ""

# Check which profile files exist and have our marker
echo "📁 Profile files:"
for profile in "$HOME/.bashrc" "$HOME/.bash_profile" "$HOME/.profile"; do
    if [[ -f "$profile" ]]; then
        if grep -qxF "$MARKER" "$profile" 2>/dev/null; then
            echo "  ✅ $profile (contains aliases)"
        else
            echo "  📄 $profile (no aliases)"
        fi
    else
        echo "  ❌ $profile (doesn't exist)"
    fi
done

echo ""
echo "🔍 Command availability:"

# Check for php
echo -n "  php: "
if command -v php >/dev/null 2>&1; then
    PHP_PATH=$(command -v php)
    echo "✅ Available at $PHP_PATH"
else
    echo "❌ Not available"
fi

# Check for php.bat
echo -n "  php.bat: "
if command -v php.bat >/dev/null 2>&1; then
    PHPBAT_PATH=$(command -v php.bat)
    echo "✅ Available at $PHPBAT_PATH"
else
    echo "❌ Not available"
fi

# Check for herd
echo -n "  herd: "
if command -v herd >/dev/null 2>&1; then
    HERD_PATH=$(command -v herd)
    echo "✅ Available at $HERD_PATH"
else
    echo "❌ Not available"
fi

# Check for herd.bat
echo -n "  herd.bat: "
if command -v herd.bat >/dev/null 2>&1; then
    HERDBAT_PATH=$(command -v herd.bat)
    echo "✅ Available at $HERDBAT_PATH"
else
    echo "❌ Not available"
fi

# Check for composer
echo -n "  composer: "
if command -v composer >/dev/null 2>&1; then
    COMPOSER_PATH=$(command -v composer)
    echo "✅ Available at $COMPOSER_PATH"
else
    echo "❌ Not available"
fi

# Check for composer.bat
echo -n "  composer.bat: "
if command -v composer.bat >/dev/null 2>&1; then
    COMPOSERBAT_PATH=$(command -v composer.bat)
    echo "✅ Available at $COMPOSERBAT_PATH"
else
    echo "❌ Not available"
fi

echo ""

# Test php -v specifically
echo "🧪 Testing 'php -v':"
if php -v >/dev/null 2>&1; then
    echo "✅ php -v works!"
    php -v | head -n 1
else
    echo "❌ php -v failed"
    echo ""
    echo "🔧 Troubleshooting steps:"
    echo "  1. Restart your Git Bash terminal completely"
    echo "  2. Check if Herd is installed: Try opening Herd application"
    echo "  3. Check Herd's PATH: In Windows, check if Herd bin directory is in PATH"
    echo "  4. Manual alias test: Run 'alias php=\"php.bat\"' then try 'php -v'"
    echo ""
    echo "💡 If still failing, run these commands and share output:"
    echo "  echo \$PATH"
    echo "  ls -la /c/Users/\$USER/AppData/Local/herd/bin/ 2>/dev/null || echo 'Herd bin not found'"
    echo "  which php.bat"
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "📋 If 'php -v' still doesn't work:"
echo "  1. Close and reopen Git Bash completely"
echo "  2. Try running this script again"
echo "  3. Check if Herd is properly installed and running"
echo "  4. Share the diagnostic output above for further troubleshooting"