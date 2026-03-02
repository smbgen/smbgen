#!/usr/bin/env bash

# Quick PHP availability test for debugging setup script issues
# Run this to see why the full setup script might fail to find PHP

echo "🔍 PHP Availability Debug Test"
echo "============================="
echo ""

echo "📋 Current Environment:"
echo "  User: $USER"
echo "  Shell: $SHELL"
echo "  Working Directory: $(pwd)"
echo ""

echo "🧪 Testing PHP availability (interactive vs script mode):"
echo ""

# Test 1: Direct command test
echo "1️⃣  Direct 'php -v' test:"
if php -v >/dev/null 2>&1; then
    echo "   ✅ SUCCESS: php -v works"
    echo "   Version: $(php -v | head -n 1)"
    echo "   Path: $(command -v php)"
else
    echo "   ❌ FAILED: php -v doesn't work"
fi
echo ""

# Test 2: Direct php.bat test
echo "2️⃣  Direct 'php.bat -v' test:"
if php.bat -v >/dev/null 2>&1; then
    echo "   ✅ SUCCESS: php.bat -v works"
    echo "   Version: $(php.bat -v | head -n 1)"
    echo "   Path: $(command -v php.bat)"
else
    echo "   ❌ FAILED: php.bat -v doesn't work"
fi
echo ""

# Test 3: command -v tests
echo "3️⃣  Command detection tests:"
echo -n "   command -v php: "
if command -v php >/dev/null 2>&1; then
    echo "✅ $(command -v php)"
else
    echo "❌ Not found"
fi

echo -n "   command -v php.bat: "
if command -v php.bat >/dev/null 2>&1; then
    echo "✅ $(command -v php.bat)"
else
    echo "❌ Not found"
fi

echo -n "   which php: "
if which php >/dev/null 2>&1; then
    echo "✅ $(which php)"
else
    echo "❌ Not found"
fi

echo -n "   which php.bat: "
if which php.bat >/dev/null 2>&1; then
    echo "✅ $(which php.bat)"
else
    echo "❌ Not found"
fi
echo ""

# Test 4: Profile file checks
echo "4️⃣  Profile file analysis:"
MARKER="# Laravel Herd aliases (clientbridge)"
for profile in "$HOME/.bash_profile" "$HOME/.bashrc" "$HOME/.profile"; do
    if [[ -f "$profile" ]]; then
        if grep -qxF "$MARKER" "$profile" 2>/dev/null; then
            echo "   ✅ $profile (contains Herd aliases)"
        else
            echo "   📄 $profile (exists, no Herd aliases)"
        fi
    else
        echo "   ❌ $profile (doesn't exist)"
    fi
done
echo ""

# Test 5: Alias checks
echo "5️⃣  Current aliases:"
if alias | grep -E "(php|composer|herd)" >/dev/null 2>&1; then
    echo "   Current relevant aliases:"
    alias | grep -E "(php|composer|herd)" | sed 's/^/   /'
else
    echo "   ❌ No relevant aliases found"
fi
echo ""

# Test 6: PATH analysis
echo "6️⃣  PATH analysis:"
echo "   Full PATH:"
echo "$PATH" | tr ':' '\n' | sed 's/^/     /'
echo ""
echo "   Herd-related paths in PATH:"
if echo "$PATH" | grep -iE "(herd|php)" >/dev/null; then
    echo "$PATH" | tr ':' '\n' | grep -iE "(herd|php)" | sed 's/^/     ✅ /'
else
    echo "     ❌ No Herd-related paths found in PATH"
fi
echo ""

# Test 7: Manual alias test
echo "7️⃣  Testing manual alias setup:"
if command -v php.bat >/dev/null 2>&1; then
    echo "   Setting up alias php='php.bat'..."
    alias php='php.bat'
    echo -n "   Testing aliased php: "
    if php -v >/dev/null 2>&1; then
        echo "✅ Works with manual alias!"
        echo "   Version: $(php -v | head -n 1)"
    else
        echo "❌ Still doesn't work with manual alias"
    fi
else
    echo "   ❌ Cannot test manual alias - php.bat not available"
fi
echo ""

echo "📋 Summary:"
echo "   If 'php -v' works directly but fails in the setup script,"
echo "   the issue is likely:"
echo "   • Profile files not being loaded correctly by the script"
echo "   • Aliases not being set up in the script environment"
echo "   • PATH differences between interactive and script mode"
echo ""
echo "💡 Solutions:"
echo "   1. Run 'bash scripts/setup-herd-gitbash.sh' first"
echo "   2. Restart Git Bash completely"
echo "   3. Try the full setup script again"
echo "   4. Share this debug output if it still fails"