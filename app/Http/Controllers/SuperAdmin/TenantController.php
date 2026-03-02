<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Jobs\InitializeTenantDatabase;
use App\Models\SubscriptionTier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        // Check if tenancy tables exist
        if (! Schema::hasTable('tenants') || ! Schema::hasTable('domains')) {
            return view('super-admin.setup-required');
        }

        $query = Tenant::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('subdomain', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $tenants = $query->latest()->paginate(25);

        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');
        $users = User::where('tenant_id', $tenant->id)->get();

        return view('super-admin.tenants.show', compact('tenant', 'users'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(StoreTenantRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create tenant following Stancl tenancy best practices
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'subdomain' => $request->subdomain,
                'custom_domain' => $request->custom_domain,
                'plan' => $request->plan,
                'trial_ends_at' => $request->trial_ends_at,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Create domain mapping for tenant
            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            $tenant->domains()->create([
                'domain' => $request->subdomain.'.'.$baseHost,
            ]);

            // Create custom domain if provided
            if ($request->filled('custom_domain')) {
                $tenant->domains()->create([
                    'domain' => $request->custom_domain,
                ]);
            }

            // Create admin user in central database
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'company_administrator',
                'is_super_admin' => false,
            ]);

            DB::commit();

            // Run tenant migrations to initialize tenant database
            // Option 1: Queue job (recommended for production/cloud environments)
            if (config('queue.default') !== 'sync') {
                InitializeTenantDatabase::dispatch($tenant->id);
                $message = 'Tenant created successfully. Database initialization queued.';
            } else {
                // Option 2: Run synchronously (local development)
                try {
                    Artisan::call('tenants:migrate', [
                        '--tenants' => [$tenant->id],
                        '--force' => true, // Required for non-interactive environments
                    ]);
                    $message = 'Tenant created and database initialized successfully.';
                } catch (\Exception $e) {
                    \Log::warning('Tenant migration failed during creation', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $message = 'Tenant created successfully, but database initialization failed. Please run migrations manually.';
                }
            }

            return redirect()
                ->route('super-admin.tenants.show', $tenant)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create tenant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create tenant: '.$e->getMessage()]);
        }
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        try {
            $tenant->update([
                'name' => $request->name,
                'email' => $request->email,
                'subdomain' => $request->subdomain,
                'custom_domain' => $request->custom_domain,
                'plan' => $request->plan,
                'trial_ends_at' => $request->trial_ends_at,
                'is_active' => $request->boolean('is_active', $tenant->is_active),
            ]);

            return redirect()
                ->route('super-admin.tenants.show', $tenant)
                ->with('success', 'Tenant updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update tenant: '.$e->getMessage()]);
        }
    }

    public function impersonate(Tenant $tenant)
    {
        $adminUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'company_administrator')
            ->first();

        if (! $adminUser) {
            return back()->withErrors(['error' => 'No admin user found for this tenant']);
        }

        session()->put('super_admin_impersonating', auth()->id());
        auth()->login($adminUser);

        return redirect()->route('admin.dashboard')->with('warning', 'You are now impersonating '.$tenant->name);
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['is_active' => false]);

        return back()->with('success', 'Tenant suspended successfully');
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['is_active' => true]);

        return back()->with('success', 'Tenant activated successfully');
    }

    public function destroy(Tenant $tenant)
    {
        try {
            // Delete all domains associated with the tenant
            $tenant->domains()->delete();

            // Delete the tenant (this will trigger tenant database deletion via events)
            $tenant->delete();

            return redirect()->route('super-admin.tenants.index')
                ->with('success', 'Tenant deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete tenant: '.$e->getMessage()]);
        }
    }

    /**
     * Add a custom domain to a tenant.
     */
    public function addDomain(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
        ]);

        try {
            $domain = Domain::create([
                'domain' => $validated['domain'],
                'tenant_id' => $tenant->id,
            ]);

            return back()->with('success', "Domain {$validated['domain']} added successfully");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to add domain: '.$e->getMessage()]);
        }
    }

    /**
     * Remove a domain from a tenant.
     */
    public function removeDomain(Tenant $tenant, Domain $domain)
    {
        if ($domain->tenant_id !== $tenant->id) {
            return back()->withErrors(['error' => 'Domain does not belong to this tenant']);
        }

        try {
            $domain->delete();

            return back()->with('success', 'Domain removed successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to remove domain: '.$e->getMessage()]);
        }
    }

    /**
     * Set a domain as the primary domain for a tenant.
     */
    public function setPrimaryDomain(Tenant $tenant, Domain $domain)
    {
        if ($domain->tenant_id !== $tenant->id) {
            return back()->withErrors(['error' => 'Domain does not belong to this tenant']);
        }

        try {
            // Update tenant data to mark this as primary
            $tenant->update([
                'primary_domain' => $domain->domain,
            ]);

            return back()->with('success', 'Primary domain updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to set primary domain: '.$e->getMessage()]);
        }
    }

    /**
     * Upgrade a tenant's subscription plan.
     */
    public function upgradePlan(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'plan' => ['required', 'string', 'in:starter,professional,enterprise'],
        ]);

        try {
            $tenant->update([
                'plan' => $validated['plan'],
                'plan_updated_at' => now(),
            ]);

            return back()->with('success', "Tenant upgraded to {$validated['plan']} plan");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to upgrade plan: '.$e->getMessage()]);
        }
    }

    /**
     * Extend a tenant's trial period.
     */
    public function extendTrial(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:90'],
        ]);

        try {
            $currentTrialEnd = $tenant->trial_ends_at ?? now();
            $newTrialEnd = $currentTrialEnd->addDays($validated['days']);

            $tenant->update([
                'trial_ends_at' => $newTrialEnd,
            ]);

            return back()->with('success', "Trial extended by {$validated['days']} days");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to extend trial: '.$e->getMessage()]);
        }
    }

    /**
     * Change a tenant's subscription tier.
     */
    public function changeTier(Request $request, Tenant $tenant): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'subscription_tier_id' => ['required', 'integer', 'exists:subscription_tiers,id'],
        ]);

        try {
            $tier = SubscriptionTier::findOrFail($validated['subscription_tier_id']);

            $tenant->update([
                'subscription_tier_id' => $tier->id,
            ]);

            return back()->with('success', "Tenant subscription changed to {$tier->name} successfully");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to change subscription tier: '.$e->getMessage()]);
        }
    }
}
