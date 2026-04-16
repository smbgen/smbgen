<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $this->redirectAuthenticatedUser($request, Auth::guard($guard)->user());
            }
        }

        if (Auth::check()) {
            return $this->redirectAuthenticatedUser($request, Auth::user());
        }

        return $next($request);
    }

    private function redirectAuthenticatedUser(Request $request, User $user): Response
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

    private function tenantAwareRedirect(Request $request, User $user, string $path): Response
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

    private function requiresDomainOnboarding(User $user): bool
    {
        if (! $this->isTenancyEnabled() || ! $this->isCompanyAdministrator($user) || empty($user->tenant_id)) {
            return false;
        }

        $tenant = Tenant::query()->find((string) $user->tenant_id);

        if (! $tenant) {
            return false;
        }

        return empty($tenant->getAttribute('domain_onboarding_completed_at'));
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
