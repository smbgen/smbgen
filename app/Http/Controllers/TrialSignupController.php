<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Stancl\Tenancy\Database\Models\Tenant;

class TrialSignupController extends Controller
{
    public function show()
    {
        return view('trial.signup');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Create tenant
            $subdomain = Str::slug($validated['company_name']).'-'.Str::lower(Str::random(4));

            // Create tenant using explicit assignment to avoid JSON data column issues
            $tenant = new Tenant;
            $tenant->id = (string) Str::uuid();
            $tenant->name = $validated['company_name'];
            $tenant->email = $validated['email'];
            $tenant->subdomain = $subdomain;
            $tenant->plan = 'trial';
            $tenant->trial_ends_at = now()->addDays(14);
            $tenant->is_active = true;
            $tenant->save();

            // Create domain mapping (subdomain-based tenancy)
            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            $tenant->domains()->create([
                'domain' => $subdomain.'.'.$baseHost,
            ]);

            // Create user as tenant admin (in central database)
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'company_administrator',
                'is_super_admin' => false,
            ]);

            // Send email verification notification
            try {
                $user->sendEmailVerificationNotification();
                \Log::info('Trial signup: Verification email sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'tenant_id' => $tenant->id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Trial signup: Failed to send verification email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Run tenant migrations
            try {
                Artisan::call('tenants:migrate', [
                    '--tenants' => [$tenant->id],
                ]);
            } catch (\Exception $e) {
                \Log::warning('Tenant migration failed during signup', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }

            DB::commit();

            // Log them in
            /** @phpstan-ignore-next-line - Auth::login() exists on SessionGuard */
            Auth::login($user);

            // Redirect to verification notice (they need to verify before accessing admin dashboard)
            return redirect()->route('verification.notice')->with('success', 'Welcome! Please verify your email to start your 14-day trial.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => 'Failed to create trial account: '.$e->getMessage()]);
        }
    }

    /**
     * Redirect to Google OAuth for trial signup
     */
    public function redirectToGoogle()
    {
        /** @phpstan-ignore-next-line - with() method exists on concrete provider implementation */
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback for trial signup
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            /** @phpstan-ignore-next-line - stateless() method exists on concrete provider implementation */
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if user already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            if ($existingUser) {
                return redirect()->route('trial.show')->withErrors([
                    'email' => 'An account with this email already exists. Please sign in instead.',
                ]);
            }

            DB::beginTransaction();

            // Generate company name from email domain or use email username
            $emailParts = explode('@', $googleUser->getEmail());
            $emailUsername = $emailParts[0];
            $emailDomain = $emailParts[1] ?? 'company';

            // Use email domain as company name (e.g., "acme.com" => "Acme")
            $companyName = ucfirst(str_replace(['.com', '.net', '.org', '.io'], '', $emailDomain));
            if (in_array($companyName, ['Gmail', 'Yahoo', 'Outlook', 'Hotmail'])) {
                // For common email providers, use the username part
                $companyName = ucwords(str_replace(['.', '_', '-'], ' ', $emailUsername));
            }

            // Create tenant
            $subdomain = Str::slug($companyName).'-'.Str::lower(Str::random(4));

            $tenant = new Tenant;
            $tenant->id = (string) Str::uuid();
            $tenant->name = $companyName;
            $tenant->email = $googleUser->getEmail();
            $tenant->subdomain = $subdomain;
            $tenant->plan = 'trial';
            $tenant->trial_ends_at = now()->addDays(14);
            $tenant->is_active = true;
            $tenant->save();

            // Create domain mapping
            $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            $tenant->domains()->create([
                'domain' => $subdomain.'.'.$baseHost,
            ]);

            // Create user as tenant admin with Google OAuth data
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(), // Google emails are verified
                'role' => 'company_administrator',
                'is_super_admin' => false,
            ]);

            // Run tenant migrations
            try {
                Artisan::call('tenants:migrate', [
                    '--tenants' => [$tenant->id],
                ]);
            } catch (\Exception $e) {
                \Log::warning('Tenant migration failed during Google trial signup', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }

            DB::commit();

            // Log them in
            /** @phpstan-ignore-next-line - Auth::login() exists on SessionGuard */
            Auth::login($user);

            \Log::info('Trial signup via Google completed', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'email' => $user->email,
            ]);

            // Redirect to admin dashboard (email already verified via Google)
            return redirect()->route('admin.dashboard')->with('success', 'Welcome! Your 14-day trial has started.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Google trial signup failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('trial.show')->withErrors([
                'error' => 'Failed to create trial account with Google: '.$e->getMessage(),
            ]);
        }
    }
}
