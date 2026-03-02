<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Database\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            \Log::info('Super Admin Dashboard accessed', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email ?? 'unknown',
                'is_super_admin' => auth()->user()->is_super_admin ?? false,
            ]);

            // Check if tenancy tables exist
            if (!$this->tenancyTablesExist()) {
                \Log::warning('Tenancy tables do not exist, showing setup page');
                return view('super-admin.setup-required');
            }

            $stats = [
                'total_tenants' => Tenant::count(),
                'active_trials' => Tenant::where('plan', 'trial')->where('is_active', true)->count(),
                'paying_customers' => Tenant::whereIn('plan', ['starter', 'professional', 'enterprise'])->where('is_active', true)->count(),
                'revenue_mrr' => $this->calculateMRR(),
            ];

            $recentTenants = Tenant::latest()->take(10)->get();

            \Log::info('Super Admin Dashboard loaded successfully');
            return view('super-admin.dashboard', compact('stats', 'recentTenants'));
        } catch (\Exception $e) {
            // Log the full error with stack trace
            \Log::error('Super Admin Dashboard Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Show detailed error for debugging (remove in production after fixing)
            return response()->json([
                'error' => 'Super Admin Dashboard Error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
                'user_info' => [
                    'id' => auth()->id(),
                    'email' => auth()->user()->email ?? 'unknown',
                    'is_super_admin' => auth()->user()->is_super_admin ?? false,
                ],
            ], 500)->header('Content-Type', 'application/json');
        }
    }
    
    /**
     * Check if required tenancy tables exist in the database.
     */
    private function tenancyTablesExist(): bool
    {
        try {
            return Schema::hasTable('tenants') && Schema::hasTable('domains');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Redirect super admin to manage master tenant site as company administrator.
     */
    public function manageMasterTenant()
    {
        try {
            $masterTenantId = env('MASTER_TENANT_ID');
            
            \Log::info('Attempting to access master tenant', [
                'master_tenant_id' => $masterTenantId,
                'request_host' => request()->getHost(),
            ]);
            
            if (!$masterTenantId) {
                \Log::warning('Master tenant ID not configured');
                return back()->with('error', 'Master tenant not configured. Set MASTER_TENANT_ID in environment variables.');
            }
            
            // Use direct DB query instead of Eloquent to avoid HasDomains trait issues
            $masterTenant = \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $masterTenantId)
                ->first();
            
            if (!$masterTenant) {
                \Log::error('Master tenant not found in database', ['id' => $masterTenantId]);
                return back()->with('error', 'Master tenant not found in database.');
            }
            
            \Log::info('Master tenant found', ['tenant' => $masterTenant->name]);
            
            // Get the primary domain using direct DB query
            $domain = \Illuminate\Support\Facades\DB::table('domains')
                ->where('tenant_id', $masterTenantId)
                ->first();
            
            if (!$domain) {
                \Log::error('Master tenant has no domain', ['tenant_id' => $masterTenantId]);
                return back()->with('error', 'Master tenant has no domain configured.');
            }
            
            \Log::info('Redirecting to master tenant admin', [
                'domain' => $domain->domain,
                'url' => 'https://' . $domain->domain . '/admin/dashboard'
            ]);
            
            // Redirect to master tenant's admin dashboard
            $protocol = app()->environment('local') ? 'http://' : 'https://';
            return redirect()->away($protocol . $domain->domain . '/admin/dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Master tenant access failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to access master tenant: ' . $e->getMessage());
        }
    }

    private function calculateMRR()
    {
        // Calculate Monthly Recurring Revenue
        $plans = [
            'starter' => 49,
            'professional' => 99,
            'enterprise' => 299,
        ];

        return Tenant::whereIn('plan', array_keys($plans))
            ->where('is_active', true)
            ->get()
            ->sum(fn ($tenant) => $plans[$tenant->plan] ?? 0);
    }
}
