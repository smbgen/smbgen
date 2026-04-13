<?php

namespace App\Http\Controllers;

use App\Jobs\InitializeTenantDatabase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TrialController extends Controller
{
    public function show()
    {
        return view('trial.signup');
    }

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'subdomain' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9\-]+$/', 'unique:tenants,subdomain'],
            'custom_domain' => ['nullable', 'string', 'max:255', 'unique:domains,domain'],
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => $request->company_name,
                'email' => $request->email,
                'subdomain' => strtolower($request->subdomain),
                'custom_domain' => $request->filled('custom_domain') ? strtolower(trim($request->custom_domain)) : null,
                'plan' => 'trial',
                'deployment_mode' => 'shared',
                'trial_ends_at' => now()->addDays(14),
                'is_active' => true,
            ]);

            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            $tenantDomain = $tenant->subdomain.'.'.$baseHost;

            $tenant->domains()->create(['domain' => $tenantDomain]);

            if ($tenant->custom_domain) {
                $tenant->domains()->create(['domain' => $tenant->custom_domain]);
            }

            User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => \App\Models\User::ROLE_TENANT_ADMIN,
                'trial_ends_at' => now()->addDays(14),
                'email_verified_at' => now(),
                'is_super_admin' => false,
            ]);

            DB::commit();

            if (config('queue.default') !== 'sync') {
                InitializeTenantDatabase::dispatch($tenant->id);
            } else {
                try {
                    Artisan::call('tenants:migrate', [
                        '--tenants' => [$tenant->id],
                        '--force' => true,
                        '--path' => database_path('migrations'),
                        '--realpath' => true,
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('Tenant migrations failed during trial registration', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?? 'https';
            $loginUrl = $scheme.'://'.$tenantDomain.'/login?email='.urlencode($request->email);

            return redirect()->away($loginUrl);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'email' => 'Unable to create your tenant account right now. Please try again.',
            ]);
        }
    }

    public function googleRedirect()
    {
        return redirect()->route('auth.google.redirect');
    }
}
