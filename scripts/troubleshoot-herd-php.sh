#!/usr/bin/env bash

# Troubleshoot Herd PHP issues in Git Bash
# Run this script if `php -v` is not working after running setup-herd-gitbash.sh

echo "🔧 Herd PHP Troubleshooting for Git Bash"
echo "========================================"
echo ""

# Check operating system
echo "💻 System Information:"
echo "  OS: $(uname -s)"
echo "  User: $USER"
echo "  Home: $HOME"
echo "  Shell: $SHELL"
if command -v node >/dev/null 2>&1; then
    NODE_VERSION=$(node -v)
    echo "  Node.js: $NODE_VERSION"
    if [[ "$NODE_VERSION" != v24.5* ]]; then
        echo "  ⚠️  WARNING: Node.js 24.5 required for Windows npm install (current: $NODE_VERSION)"
    fi
else
    echo "  Node.js: Not installed"
fi
echo ""

# Check profile files
echo "📁 Profile Files Status:"
MARKER="# Laravel Herd aliases (clientbridge)"
for profile in "$HOME/.bashrc" "$HOME/.bash_profile" "$HOME/.profile"; do
    if [[ -f "$profile" ]]; then
        if grep -qxF "$MARKER" "$profile" 2>/dev/null; then
            echo "  ✅ $profile (contains Herd aliases)"
        else
            echo "  📄 $profile (exists, no aliases)"
        fi
    else
        echo "  ❌ $profile (doesn't exist)"
    fi
done
echo ""

# Check PATH
echo "🛤️  Current PATH:"
echo "$PATH" | tr ':' '\n' | grep -E "(herd|php|composer)" || echo "  ❌ No Herd-related paths found"
echo ""

# Common Herd installation paths
echo "📂 Checking common Herd installation paths:"
HERD_PATHS=(
    "/c/Users/$USER/AppData/Local/herd/bin"
    "/c/Users/$USER/AppData/Local/Laravel/Herd/bin" 
    "/c/Program Files/Laravel/Herd/bin"
    "/c/Program Files (x86)/Laravel/Herd/bin"
    "/c/Laravel/Herd/bin"
    "/c/herd/bin"
)

FOUND_HERD_PATH=""
for path in "${HERD_PATHS[@]}"; do
    if [[ -d "$path" ]]; then
        echo "  ✅ Found: $path"
        FOUND_HERD_PATH="$path"
        echo "    Contents:"
        ls -la "$path" | head -10
        break
    else
        echo "  ❌ Not found: $path"
    fi
done
echo ""

# Check if executables exist
echo "🔍 Executable Availability:"
COMMANDS=("php" "php.bat" "herd" "herd.bat" "composer" "composer.bat")
for cmd in "${COMMANDS[@]}"; do
    if command -v "$cmd" >/dev/null 2>&1; then
        CMD_PATH=$(command -v "$cmd")
        echo "  ✅ $cmd: $CMD_PATH"
    else
        echo "  ❌ $cmd: Not found"
    fi
done
echo ""

# Test PHP versions
echo "🧪 PHP Version Tests:"
for php_cmd in "php" "php.bat"; do
    echo -n "  Testing '$php_cmd -v': "
    if command -v "$php_cmd" >/dev/null 2>&1; then
        if $php_cmd -v >/dev/null 2>&1; then
            VERSION=$($php_cmd -v 2>/dev/null | head -n 1)
            echo "✅ $VERSION"
        else
            echo "❌ Command exists but fails to run"
        fi
    else
        echo "❌ Command not found"
    fi
done
echo ""

# Check if Herd is running
echo "🏃 Herd Service Status:"
if command -v herd >/dev/null 2>&1; then
    echo "  Testing 'herd --version':"
    if herd --version >/dev/null 2>&1; then
        HERD_VERSION=$(herd --version 2>/dev/null)
        echo "  ✅ $HERD_VERSION"
    else
        echo "  ❌ Herd command exists but fails"
    fi
elif command -v herd.bat >/dev/null 2>&1; then
    echo "  Testing 'herd.bat --version':"
    if herd.bat --version >/dev/null 2>&1; then
        HERD_VERSION=$(herd.bat --version 2>/dev/null)
        echo "  ✅ $HERD_VERSION"
    else
        echo "  ❌ herd.bat command exists but fails"
    fi
else
    echo "  ❌ No herd command found"
fi
echo ""

# Provide solutions
echo "🔧 Troubleshooting Solutions:"
echo ""

if [[ -z "$FOUND_HERD_PATH" ]]; then
    echo "❌ PROBLEM: Herd installation not found"
    echo "   SOLUTION: Install Laravel Herd from https://herd.laravel.com/windows"
    echo ""
else
    echo "✅ Herd installation found at: $FOUND_HERD_PATH"
    
    # Check if it's in PATH
    if echo "$PATH" | grep -q "$FOUND_HERD_PATH"; then
        echo "✅ Herd path is in system PATH"
    else
        echo "❌ PROBLEM: Herd path not in system PATH"
        echo "   SOLUTION 1: Add to Windows PATH environment variable:"
        echo "   - Open Windows Settings > System > About > Advanced System Settings"
        echo "   - Click 'Environment Variables'"
        echo "   - Edit 'Path' in System Variables"
        echo "   - Add: $FOUND_HERD_PATH"
        echo ""
        echo "   SOLUTION 2: Temporary fix for this session:"
        echo "   export PATH=\"$FOUND_HERD_PATH:\$PATH\""
        echo ""
    fi
fi

# Check aliases
if ! command -v php >/dev/null 2>&1; then
    if command -v php.bat >/dev/null 2>&1; then
        echo "💡 QUICK FIX: Run this command to enable php for this session:"
        echo "   alias php='php.bat'"
        echo ""
    fi
fi

echo "🔄 GENERAL SOLUTIONS:"
echo "1. Restart Git Bash completely (close all windows, reopen)"
echo "2. Restart your computer (Windows PATH changes require restart)"
echo "3. Run the setup script again: bash scripts/setup-herd-gitbash.sh"
echo "4. Check if Herd application is running in Windows"
echo ""

echo "📋 If still not working, share this diagnostic output for help!"