#!/bin/bash

# Get the project root directory (parent of deployment/)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "=== VPS Migration Conflict Resolver ==="
echo "This will fix migration conflicts on VPS"
echo "======================================"
echo "📂 Project root: $PROJECT_ROOT"
echo

# Change to project root
cd "$PROJECT_ROOT"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: Not in Laravel project directory"
    exit 1
fi

echo "🔍 Checking migration status..."
php artisan migrate:status

echo
echo "🔧 Fixing renamed migration conflicts..."

# Use PHP artisan tinker to manually mark migrations as run
php artisan tinker --execute="
use Illuminate\Support\Facades\DB;

// Mark the renamed migrations as run without executing them
\$migrations = [
    '2025_10_02_000003_create_blackout_dates_table',
    '2025_10_02_000004_add_booking_settings_to_availabilities_table'
];

foreach (\$migrations as \$migration) {
    \$exists = DB::table('migrations')->where('migration', \$migration)->exists();
    if (!\$exists) {
        DB::table('migrations')->insert([
            'migration' => \$migration,
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo \"✅ Marked \$migration as run\\n\";
    } else {
        echo \"⚠️  \$migration already exists\\n\";
    }
}

echo \"\\n🎉 Migration conflicts resolved!\\n\";
"

echo
echo "🔄 Running any pending migrations..."
php artisan migrate --force

echo
echo "✅ Final migration status:"
php artisan migrate:status

echo
echo "🎉 Migration conflict resolution complete!"