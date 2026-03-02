#!/bin/bash
# Fix tenancy setup on Laravel Cloud

echo "=== STEP 1: Clear all caches ==="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "=== STEP 2: Run central database migrations (tenants & domains tables) ==="
php artisan migrate --force

echo ""
echo "=== STEP 3: Check if tenancy tables exist ==="
php artisan tinker <<'EOF'
echo "Tenants table exists: " . (\Illuminate\Support\Facades\Schema::hasTable('tenants') ? "YES" : "NO") . "\n";
echo "Domains table exists: " . (\Illuminate\Support\Facades\Schema::hasTable('domains') ? "YES" : "NO") . "\n";
EOF

echo ""
echo "=== STEP 4: Check existing tenants ==="
php artisan tinker <<'EOF'
$tenants = App\Models\Tenant::with('domains')->get();
echo "Total tenants: " . $tenants->count() . "\n";
foreach ($tenants as $tenant) {
    echo "  Tenant: {$tenant->name} (ID: {$tenant->id})\n";
    foreach ($tenant->domains as $domain) {
        echo "    Domain: {$domain->domain}\n";
    }
}
EOF

echo ""
echo "=== STEP 5: Test a simple route ==="
curl -I https://smbgen.com/

echo ""
echo "=== Done! Check output above for errors ==="
echo ""
echo "If tables don't exist, you need to run migrations."
echo "If tenants don't exist, you need to create master tenant (see MASTER_TENANT_SETUP.md)"
