<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnosticsController extends Controller
{
    public function index()
    {
        $tenantsTableExists = Schema::hasTable('tenants');
        $domainsTableExists = Schema::hasTable('domains');

        $diagnostics = [
            'tenancy_enabled' => (bool) env('TENANCY_ENABLED', false),
            'tenancy_resolver' => (string) config('tenancy.resolver', env('TENANCY_RESOLVER', 'domain')),
            'tenants_table_exists' => $tenantsTableExists,
            'domains_table_exists' => $domainsTableExists,
            'tenant_count' => 0,
            'active_tenants' => 0,
            'domain_count' => 0,
            'user_count' => 0,
            'database_driver' => config('database.default'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'queue_driver' => config('queue.default'),
            'cache_driver' => config('cache.default'),
            'providers' => [
                'tenancy_loaded' => class_exists(\Stancl\Tenancy\TenancyServiceProvider::class),
            ],
            'tenant_context' => [
                'is_initialized' => app()->bound('currentTenant'),
                'current_tenant_id' => app()->bound('currentTenant') ? tenant('id') : 'NONE',
            ],
            'middleware' => [],
            'tenants' => [],
            'domains' => [],
        ];

        try {
            $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
            $middlewareGroups = $kernel->getMiddlewareGroups();
            $diagnostics['middleware']['tenant_group_exists'] = isset($middlewareGroups['tenant']);
            $diagnostics['middleware']['tenant_middleware'] = $middlewareGroups['tenant'] ?? [];
        } catch (\Exception $e) {
            $diagnostics['middleware']['error'] = $e->getMessage();
        }

        if ($tenantsTableExists) {
            try {
                $diagnostics['tenant_count'] = (int) DB::table('tenants')->count();
                $diagnostics['active_tenants'] = (int) DB::table('tenants')->where('is_active', true)->count();
                $diagnostics['tenants'] = DB::table('tenants')
                    ->select('id', 'name', 'email', 'subdomain', 'plan', 'is_active', 'created_at')
                    ->latest()
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                $diagnostics['tenants_error'] = $e->getMessage();
            }
        }

        if ($domainsTableExists) {
            try {
                $diagnostics['domain_count'] = (int) DB::table('domains')->count();
                $diagnostics['domains'] = DB::table('domains')
                    ->select('id', 'domain', 'tenant_id', 'created_at')
                    ->latest()
                    ->limit(20)
                    ->get();
            } catch (\Exception $e) {
                $diagnostics['domains_error'] = $e->getMessage();
            }
        }

        if (Schema::hasTable('users')) {
            $diagnostics['user_count'] = (int) DB::table('users')->count();
        }

        return view('super-admin.diagnostics', compact('diagnostics'));
    }

    public function runMigrations(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return back()->with('success', 'Migrations completed successfully.')
                ->with('migration_output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: '.$e->getMessage());
        }
    }

    public function createMasterTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'domain' => 'required|string|max:255',
        ]);

        if (! env('TENANCY_ENABLED')) {
            return back()->with('error', 'TENANCY_ENABLED is not set to true in your environment.');
        }

        try {
            DB::beginTransaction();

            if (! Schema::hasTable('tenants')) {
                throw new \Exception('Tenants table does not exist. Run migrations first.');
            }

            $tenantId = \Illuminate\Support\Str::uuid()->toString();

            DB::table('tenants')->insert([
                'id' => $tenantId,
                'name' => $request->name,
                'email' => $request->email,
                'plan' => 'enterprise',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('domains')->insert([
                'domain' => $request->domain,
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()
                ->with('success', 'Master tenant created successfully!')
                ->with('master_tenant_id', $tenantId)
                ->with('instruction', 'Add this to your .env: MASTER_TENANT_ID='.$tenantId);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create master tenant: '.$e->getMessage());
        }
    }

    public function clearCaches(Request $request)
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return back()->with('success', 'All caches cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cache clear failed: '.$e->getMessage());
        }
    }

    public function runTenantMigrations(Request $request)
    {
        try {
            if ($request->filled('tenant_id')) {
                $tenant = DB::table('tenants')->where('id', $request->tenant_id)->first();

                if (! $tenant) {
                    throw new \Exception('Tenant not found.');
                }

                Artisan::call('tenants:migrate', ['--tenants' => [$request->tenant_id]]);

                return back()->with('success', 'Tenant migrations completed for: '.$tenant->name)
                    ->with('migration_output', Artisan::output());
            }

            Artisan::call('tenants:migrate', ['--force' => true]);
            $output = Artisan::output();

            return back()->with('success', 'Tenant migrations completed for all tenants.')
                ->with('migration_output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Tenant migration failed: '.$e->getMessage());
        }
    }
}
