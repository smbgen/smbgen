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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $tenantsTableExists = Schema::hasTable('tenants');
        $domainsTableExists = Schema::hasTable('domains');

        if (! $tenantsTableExists || ! $domainsTableExists) {
            return view('super-admin.setup-required', compact('tenantsTableExists', 'domainsTableExists'));
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

        $stats = [
            'total' => Tenant::query()->count(),
            'active' => Tenant::query()->where('is_active', true)->count(),
            'trial' => Tenant::query()->where('plan', 'trial')->count(),
            'suspended' => Tenant::query()->where('is_active', false)->count(),
        ];

        return view('super-admin.tenants.index', compact('tenants', 'stats'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains', 'subscriptionTier');
        $users = User::where('tenant_id', $tenant->id)->get();

        return view('super-admin.tenants.show', compact('tenant', 'users'));
    }

    public function create()
    {
        $tiers = SubscriptionTier::active()->ordered()->get();

        return view('super-admin.tenants.create', compact('tiers'));
    }

    public function store(StoreTenantRequest $request)
    {
        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'subdomain' => $request->subdomain,
                'custom_domain' => $request->custom_domain ?: null,
                'plan' => $request->plan,
                'deployment_mode' => $request->deployment_mode,
                'trial_ends_at' => $request->trial_ends_at ?: now()->addDays(14),
                'is_active' => $request->boolean('is_active', true),
            ]);

            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            $tenant->domains()->create([
                'domain' => $request->subdomain.'.'.$baseHost,
            ]);

            if ($request->filled('custom_domain')) {
                $tenant->domains()->create([
                    'domain' => $request->custom_domain,
                ]);
            }

            User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'company_administrator',
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]);

            DB::commit();

            if (config('queue.default') !== 'sync') {
                InitializeTenantDatabase::dispatch($tenant->id);
                $message = 'Tenant created. Database initialization queued.';
            } else {
                try {
                    Artisan::call('tenants:migrate', [
                        '--tenants' => [$tenant->id],
                        '--force' => true,
                    ]);
                    $message = 'Tenant created and database initialized successfully.';
                } catch (\Exception $e) {
                    \Log::warning('Tenant migration failed during creation', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage(),
                    ]);
                    $message = 'Tenant created, but database initialization failed. Run migrations manually.';
                }
            }

            return redirect()
                ->route('super-admin.tenants.show', $tenant)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create tenant', ['error' => $e->getMessage()]);

            return back()->withInput()->withErrors(['error' => 'Failed to create tenant: '.$e->getMessage()]);
        }
    }

    public function edit(Tenant $tenant)
    {
        $tiers = SubscriptionTier::active()->ordered()->get();

        return view('super-admin.tenants.edit', compact('tenant', 'tiers'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        try {
            $tenant->update([
                'name' => $request->name,
                'email' => $request->email,
                'subdomain' => $request->subdomain,
                'custom_domain' => $request->custom_domain ?: null,
                'plan' => $request->plan,
                'deployment_mode' => $request->deployment_mode,
                'trial_ends_at' => $request->trial_ends_at ?: null,
                'is_active' => $request->boolean('is_active', $tenant->is_active),
            ]);

            return redirect()
                ->route('super-admin.tenants.show', $tenant)
                ->with('success', 'Tenant updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to update tenant', ['tenant_id' => $tenant->id, 'error' => $e->getMessage()]);

            return back()->withInput()->withErrors(['error' => 'Failed to update tenant: '.$e->getMessage()]);
        }
    }

    public function impersonate(Request $request, Tenant $tenant)
    {
        $adminUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'company_administrator')
            ->first();

        if (! $adminUser) {
            return back()->withErrors(['error' => 'No admin user found for this tenant.']);
        }

        $request->session()->put('super_admin_impersonating', [
            'super_admin_id' => auth()->id(),
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
        ]);

        auth()->login($adminUser);
        $request->session()->regenerate();

        return redirect()->away($this->tenantAdminDashboardUrl($tenant))
            ->with('warning', 'You are now impersonating '.$tenant->name);
    }

    public function stopImpersonating(Request $request)
    {
        $impersonation = $request->session()->pull('super_admin_impersonating');

        $superAdminId = is_array($impersonation)
            ? ($impersonation['super_admin_id'] ?? null)
            : $impersonation;

        if (! $superAdminId) {
            return redirect()->route('super-admin.dashboard');
        }

        $superAdmin = User::find($superAdminId);

        if (! $superAdmin) {
            return redirect()->route('super-admin.dashboard');
        }

        auth()->login($superAdmin);
        $request->session()->regenerate();

        return redirect()->away($this->centralSuperAdminUrl())
            ->with('success', 'Impersonation ended. Welcome back.');
    }

    private function tenantAdminDashboardUrl(Tenant $tenant): string
    {
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?? 'https';

        return $scheme.'://'.$this->tenantHost($tenant).'/admin/dashboard';
    }

    private function centralSuperAdminUrl(): string
    {
        return URL::route('super-admin.tenants.index', absolute: true);
    }

    private function tenantHost(Tenant $tenant): string
    {
        if ($tenant->custom_domain) {
            return $tenant->custom_domain;
        }

        $firstDomain = $tenant->domains()->orderBy('id')->value('domain');

        if ($firstDomain) {
            return $firstDomain;
        }

        $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';

        return $tenant->subdomain.'.'.$baseHost;
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['is_active' => false]);

        return back()->with('success', 'Tenant suspended successfully.');
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['is_active' => true]);

        return back()->with('success', 'Tenant activated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $tenant->domains()->delete();
            $tenant->delete();

            return redirect()->route('super-admin.tenants.index')
                ->with('success', 'Tenant deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete tenant: '.$e->getMessage()]);
        }
    }

    public function addDomain(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $normalizedDomain = strtolower(trim($validated['domain']));
        $normalizedDomain = preg_replace('#^https?://#', '', $normalizedDomain) ?? $normalizedDomain;
        $normalizedDomain = rtrim($normalizedDomain, '/');

        if ($normalizedDomain === '' || ! preg_match('/^[a-z0-9.-]+$/', $normalizedDomain)) {
            return back()->withErrors(['domain' => 'Enter a valid domain (letters, numbers, dots, and hyphens only).']);
        }

        if (Domain::query()->where('domain', $normalizedDomain)->exists()) {
            return back()->withErrors(['domain' => 'This domain is already in use.']);
        }

        try {
            Domain::create([
                'domain' => $normalizedDomain,
                'tenant_id' => $tenant->id,
            ]);

            return back()->with('success', "Domain {$normalizedDomain} added successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to add domain: '.$e->getMessage()]);
        }
    }

    public function removeDomain(Tenant $tenant, Domain $domain)
    {
        if ((string) $domain->tenant_id !== (string) $tenant->id) {
            abort(403, 'This domain does not belong to the selected tenant.');
        }

        $domain->delete();

        return back()->with('success', 'Domain removed successfully.');
    }

    public function setPrimaryDomain(Tenant $tenant, Domain $domain)
    {
        if ((string) $domain->tenant_id !== (string) $tenant->id) {
            abort(403, 'This domain does not belong to the selected tenant.');
        }

        $tenant->update(['custom_domain' => $domain->domain]);

        return back()->with('success', 'Primary domain updated.');
    }

    public function extendTrial(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'trial_ends_at' => ['required', 'date', 'after:today'],
        ]);

        $tenant->update(['trial_ends_at' => $validated['trial_ends_at']]);

        return back()->with('success', 'Trial updated successfully.');
    }

    public function changeTier(Request $request, Tenant $tenant)
    {
        if ($request->filled('subscription_tier_id')) {
            $validated = $request->validate([
                'subscription_tier_id' => ['required', 'integer', 'exists:subscription_tiers,id'],
            ]);

            $tier = SubscriptionTier::findOrFail($validated['subscription_tier_id']);
            $tenant->update([
                'subscription_tier_id' => $tier->id,
                'plan' => $tier->slug,
            ]);

            return back()->with('success', "Subscription changed to {$tier->name} successfully.");
        }

        $validated = $request->validate([
            'plan' => ['required', 'string', 'in:trial,starter,professional,enterprise'],
        ]);

        $tenant->update(['plan' => $validated['plan']]);

        return back()->with('success', 'Plan changed successfully.');
    }

    public function storeUser(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:company_administrator,team_member,client,staff'],
        ]);

        try {
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'is_super_admin' => false,
            ]);

            return back()->with('success', 'User added successfully. Verify their email to activate.');
        } catch (\Exception $e) {
            \Log::error('Failed to add user to tenant', ['tenant_id' => $tenant->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Failed to add user: '.$e->getMessage()]);
        }
    }

    public function verifyUser(Tenant $tenant, User $user)
    {
        if ((string) $user->tenant_id !== (string) $tenant->id) {
            abort(403, 'This user does not belong to the selected tenant.');
        }

        try {
            $user->update(['email_verified_at' => now()]);

            return back()->with('success', ucfirst($user->name).'\'s email has been verified.');
        } catch (\Exception $e) {
            \Log::error('Failed to verify user', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Failed to verify user: '.$e->getMessage()]);
        }
    }

    public function removeUser(Tenant $tenant, User $user)
    {
        if ((string) $user->tenant_id !== (string) $tenant->id) {
            abort(403, 'This user does not belong to the selected tenant.');
        }

        try {
            $user->delete();

            return back()->with('success', 'User removed from tenant successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to remove user from tenant', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Failed to remove user: '.$e->getMessage()]);
        }
    }
}

