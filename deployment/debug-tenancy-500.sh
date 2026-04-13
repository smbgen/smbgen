#!/bin/bash
# Debug tenancy 500 errors on Laravel Cloud

echo "=== CHECKING LARAVEL LOGS ==="
tail -n 100 storage/logs/laravel.log

echo ""
echo "=== CHECKING ENVIRONMENT ==="
php artisan config:show tenancy

echo ""
echo "=== CHECKING TENANCY TABLES EXIST ==="
php artisan tinker <<'EOF'
try {
    echo "Checking tenants table: ";
    echo \Illuminate\Support\Facades\Schema::hasTable('tenants') ? "EXISTS\n" : "MISSING\n";
    
    echo "Checking domains table: ";
    echo \Illuminate\Support\Facades\Schema::hasTable('domains') ? "EXISTS\n" : "MISSING\n";
    
    if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
        $count = DB::table('tenants')->count();
        echo "Total tenants: $count\n";
        
        if ($count > 0) {
            $tenants = DB::table('tenants')->select('id', 'name', 'subdomain', 'custom_domain')->get();
            foreach ($tenants as $t) {
                echo "  - {$t->name} (id: {$t->id}, subdomain: {$t->subdomain})\n";
            }
        }
    }
    
    if (\Illuminate\Support\Facades\Schema::hasTable('domains')) {
        $count = DB::table('domains')->count();
        echo "Total domains: $count\n";
        
        if ($count > 0) {
            $domains = DB::table('domains')->select('domain', 'tenant_id')->get();
            foreach ($domains as $d) {
                echo "  - {$d->domain} -> {$d->tenant_id}\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
EOF

echo ""
echo "=== CHECKING ROUTE LIST (sample) ==="
php artisan route:list | head -20

echo ""
echo "=== TESTING TENANCY MIDDLEWARE ==="
php artisan tinker <<'EOF'
try {
    // Check if tenancy service provider is loaded
    $providers = app()->getLoadedProviders();
    echo "Tenancy provider loaded: ";
    echo isset($providers['Stancl\Tenancy\TenancyServiceProvider']) ? "YES\n" : "NO\n";
    
    // Check middleware
    $middleware = app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups();
    echo "Tenant middleware group exists: ";
    echo isset($middleware['tenant']) ? "YES\n" : "NO\n";
    
    if (isset($middleware['tenant'])) {
        echo "Tenant middleware contains:\n";
        foreach ($middleware['tenant'] as $m) {
            echo "  - $m\n";
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
EOF
