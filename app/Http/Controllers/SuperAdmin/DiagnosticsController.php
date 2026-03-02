<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Database\Models\Domain;

class DiagnosticsController extends Controller
{
    public function index()
    {
        $diagnostics = [
            'environment' => [
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'tenancy_enabled' => env('TENANCY_ENABLED', 'NOT SET'),
                'tenancy_central_domains' => env('TENANCY_CENTRAL_DOMAINS', 'NOT SET'),
                'master_tenant_id' => env('MASTER_TENANT_ID', 'NOT SET'),
                'current_domain' => request()->getHost(),
                'database_connection' => config('database.default'),
            ],
            'tables' => [
                'tenants_exists' => Schema::hasTable('tenants'),
                'domains_exists' => Schema::hasTable('domains'),
            ],
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

        // Check middleware
        try {
            $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
            $middlewareGroups = $kernel->getMiddlewareGroups();
            $diagnostics['middleware']['tenant_group_exists'] = isset($middlewareGroups['tenant']);
            $diagnostics['middleware']['tenant_middleware'] = $middlewareGroups['tenant'] ?? [];
        } catch (\Exception $e) {
            $diagnostics['middleware']['error'] = $e->getMessage();
        }

        // Get tenants if table exists
        if ($diagnostics['tables']['tenants_exists']) {
            try {
                $diagnostics['tenants_count'] = DB::table('tenants')->count();
                $diagnostics['tenants'] = DB::table('tenants')
                    ->select('id', 'name', 'email', 'plan', 'is_active', 'created_at')
                    ->get()
                    ->map(function ($tenant) {
                        return (array) $tenant;
                    })
                    ->toArray();
            } catch (\Exception $e) {
                $diagnostics['tenants_error'] = $e->getMessage();
            }
        }

        // Get domains if table exists
        if ($diagnostics['tables']['domains_exists']) {
            try {
                $diagnostics['domains_count'] = DB::table('domains')->count();
                $diagnostics['domains'] = DB::table('domains')
                    ->select('id', 'domain', 'tenant_id', 'created_at')
                    ->get()
                    ->map(function ($domain) {
                        return (array) $domain;
                    })
                    ->toArray();
            } catch (\Exception $e) {
                $diagnostics['domains_error'] = $e->getMessage();
            }
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
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    public function createMasterTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'domain' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Check if tenants table exists
            if (!Schema::hasTable('tenants')) {
                throw new \Exception('Tenants table does not exist. Run migrations first.');
            }

            // Check if tenancy is enabled
            if (!env('TENANCY_ENABLED')) {
                throw new \Exception('TENANCY_ENABLED is not set to true in environment variables.');
            }

            // Create tenant using direct DB insert first
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

            // Create domain
            DB::table('domains')->insert([
                'domain' => $request->domain,
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Master tenant created successfully!')
                ->with('master_tenant_id', $tenantId)
                ->with('instruction', 'Add this to your environment variables: MASTER_TENANT_ID=' . $tenantId);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create master tenant: ' . $e->getMessage());
        }
    }

    public function clearCaches(Request $request)
    {
        try {
            $output = [];
            
            Artisan::call('config:clear');
            $output[] = 'Config cache cleared';
            
            Artisan::call('cache:clear');
            $output[] = 'Application cache cleared';
            
            Artisan::call('route:clear');
            $output[] = 'Route cache cleared';
            
            Artisan::call('view:clear');
            $output[] = 'View cache cleared';

            return back()->with('success', 'All caches cleared successfully.')
                ->with('cache_output', implode("\n", $output));
        } catch (\Exception $e) {
            return back()->with('error', 'Cache clear failed: ' . $e->getMessage());
        }
    }

    public function runTenantMigrations(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|uuid',
        ]);

        try {
            // Get tenant directly from database
            $tenant = DB::table('tenants')->where('id', $request->tenant_id)->first();
            
            if (!$tenant) {
                throw new \Exception('Tenant not found');
            }
            
            Artisan::call('tenants:migrate', [
                '--tenants' => [$request->tenant_id],
            ]);
            
            $output = Artisan::output();
            
            return back()->with('success', 'Tenant migrations completed for: ' . $tenant->name)
                ->with('migration_output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Tenant migration failed: ' . $e->getMessage());
        }
    }
}
