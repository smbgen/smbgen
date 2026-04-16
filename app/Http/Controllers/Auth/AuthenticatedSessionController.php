<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        if (config('app.debug')) {
            return redirect()->route('debug.switch-user');
        }

        $tenancyEnabled = (bool) config('app.tenancy_enabled', false);

        return view($tenancyEnabled ? 'auth.login' : 'auth.login-standard', [
            'tenancyEnabled' => $tenancyEnabled,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Clear any intended URL to prevent unwanted redirects
        $request->session()->forget('url.intended');

        // Log the login
        \App\Services\ActivityLogger::logLogin();

        // Redirect based on user role
        $user = auth()->user();

        // Mark account as activated on first login for client users
        if ($user->role === 'client') {
            $client = \App\Models\Client::where('email', $user->email)->first();
            if ($client && ! $client->account_activated_at) {
                $client->account_activated_at = now();
                $client->last_login_at = now();
                $client->save();
                \App\Services\ActivityLogger::log(
                    action: 'account_activated',
                    description: 'Client account activated on first login',
                    subject: $client
                );
            } elseif ($client) {
                $client->last_login_at = now();
                $client->save();
            }
        }

        return $this->resolvePostLoginRedirect($request, $user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log the logout before destroying the session
        \App\Services\ActivityLogger::logLogout();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        \Log::info('Initiating Google Login OAuth redirect', [
            'configured_base_redirect' => config('services.google.redirect'),
            'app_url' => config('app.url'),
            'client_id_configured' => ! empty(config('services.google.client_id')),
            'client_id_value' => config('services.google.client_id'),
            'client_secret_configured' => ! empty(config('services.google.client_secret')),
            'request_url' => request()->url(),
            'request_full_url' => request()->fullUrl(),
        ]);

        try {
            $redirectResponse = Socialite::driver('google')->redirect();
            $actualRedirectUrl = $redirectResponse->getTargetUrl();

            \Log::info('Google Login OAuth redirect URL generated', [
                'full_redirect_url' => $actualRedirectUrl,
                'url_length' => strlen($actualRedirectUrl),
            ]);

            return $redirectResponse;
        } catch (\Exception $e) {
            \Log::error('Failed to generate Google Login OAuth redirect', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        Log::info('Handling Google callback');
        Log::info('Request parameters:', $request->all());
        Log::info('Request URL:', [$request->fullUrl()]);

        try {
            // Check if Socialite is working
            Log::info('Attempting to get Google user...');
            $googleUser = Socialite::driver('google')->user();
            Log::info('Google User object', ['email' => $googleUser->getEmail(), 'name' => $googleUser->getName(), 'id' => $googleUser->getId()]);

            // Check if User model exists and has required fields
            Log::info('Checking User model...');
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'role' => User::ROLE_TENANT_ADMIN,
                    'password' => Hash::make(Str::random(32)), // unguessable; user signs in via Google
                ]
            );

            // Update google_id if user exists but doesn't have it
            if (! $user->google_id) {
                Log::info('Updating existing user with google_id');
                $user->update(['google_id' => $googleUser->getId()]);
            }

            $user->ensureClientRecord();

            if (! $user->hasVerifiedEmail()) {
                if ($user->markEmailAsVerified()) {
                    event(new Verified($user));
                }

                Log::info('User email auto-verified after Google OAuth login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }

            Log::info('User logged in', ['user_id' => $user->id, 'email' => $user->email]);

            Auth::login($user);

            // Log the Google OAuth login
            \App\Services\ActivityLogger::log(
                action: 'login_google',
                description: 'User logged in via Google OAuth',
                subject: null,
                properties: ['provider' => 'google', 'google_id' => $googleUser->getId()],
                userId: $user->id
            );

            // Redirect based on user role
            return $this->resolvePostLoginRedirect($request, $user);

        } catch (\Exception $e) {
            Log::error('Google callback error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->withErrors(['email' => 'Google authentication failed: '.$e->getMessage()]);
        }
    }

    private function requiresDomainOnboarding(User $user): bool
    {
        if (! $this->isTenancyEnabled()) {
            return false;
        }

        if (! $this->isCompanyAdministrator($user) || empty($user->tenant_id)) {
            return false;
        }

        $tenant = Tenant::query()->find((string) $user->tenant_id);

        if (! $tenant) {
            return false;
        }

        return empty($tenant->getAttribute('domain_onboarding_completed_at'));
    }

    private function resolvePostLoginRedirect(Request $request, User $user): RedirectResponse
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($this->isCompanyAdministrator($user)) {
            $targetPath = $this->requiresDomainOnboarding($user)
                ? '/admin/domain-onboarding'
                : '/admin/dashboard';

            return $this->tenantAwareRedirect($request, $user, $targetPath);
        }

        return $this->tenantAwareRedirect($request, $user, '/dashboard');
    }

    private function tenantAwareRedirect(Request $request, User $user, string $path): RedirectResponse
    {
        if (! $this->isTenancyEnabled() || empty($user->tenant_id)) {
            return redirect($path);
        }

        $tenantHost = $this->resolveTenantHost($user);

        if (! $tenantHost || strtolower($tenantHost) === strtolower($request->getHost())) {
            return redirect($path);
        }

        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?? $request->getScheme();

        return redirect()->away($scheme.'://'.$tenantHost.$path);
    }

    private function resolveTenantHost(User $user): ?string
    {
        if (empty($user->tenant_id)) {
            return null;
        }

        $tenant = Tenant::query()->find((string) $user->tenant_id);

        if (! $tenant) {
            return null;
        }

        if ($tenant->custom_domain) {
            return (string) $tenant->custom_domain;
        }

        $firstDomain = $tenant->domains()->orderBy('id')->value('domain');

        if ($firstDomain) {
            return (string) $firstDomain;
        }

        $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';

        return $tenant->subdomain.'.'.$baseHost;
    }

    private function isCompanyAdministrator(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMINISTRATOR, User::ROLE_ADMINISTRATOR_LEGACY], true);
    }

    private function isTenancyEnabled(): bool
    {
        $envFlag = filter_var((string) env('TENANCY_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN);

        return (bool) config('app.tenancy_enabled', false) || $envFlag;
    }
}
