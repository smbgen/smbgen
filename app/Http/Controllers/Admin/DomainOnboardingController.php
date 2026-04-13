<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stancl\Tenancy\Database\Models\Domain;

class DomainOnboardingController extends Controller
{
    public function show(Request $request): View
    {
        $tenant = $this->resolveTenant($request);

        $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
        $platformDomain = $tenant->subdomain.'.'.$baseHost;
        $status = $this->domainStatus($tenant);

        return view('admin.domain-onboarding.index', [
            'tenant' => $tenant,
            'platformDomain' => $platformDomain,
            'status' => $status,
            'allDomains' => $tenant->domains()->orderBy('domain')->pluck('domain'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $tenant = $this->resolveTenant($request);
        $action = (string) $request->input('action', 'save_domain');

        if ($action === 'use_subdomain') {
            $this->updateTenantData($tenant, [
                'custom_domain_status' => 'using_subdomain',
                'domain_onboarding_completed_at' => now()->toIso8601String(),
            ]);

            return back()->with('success', 'Onboarding completed. You can keep using your platform subdomain.');
        }

        if ($action === 'mark_verified') {
            if (! $tenant->custom_domain) {
                return back()->withErrors([
                    'custom_domain' => 'Add a custom domain first before marking it verified.',
                ]);
            }

            $this->updateTenantData($tenant, [
                'custom_domain_status' => 'verified',
                'custom_domain_verified_at' => now()->toIso8601String(),
                'custom_domain_last_checked_at' => now()->toIso8601String(),
                'domain_onboarding_completed_at' => now()->toIso8601String(),
            ]);

            return back()->with('success', 'Custom domain marked as verified.');
        }

        $validated = $request->validate([
            'custom_domain' => ['required', 'string', 'max:255'],
        ]);

        $normalizedDomain = strtolower(trim($validated['custom_domain']));
        $normalizedDomain = preg_replace('#^https?://#', '', $normalizedDomain) ?? $normalizedDomain;
        $normalizedDomain = rtrim($normalizedDomain, '/');

        if ($normalizedDomain === '' || ! preg_match('/^[a-z0-9.-]+$/', $normalizedDomain)) {
            return back()->withErrors([
                'custom_domain' => 'Enter a valid domain (letters, numbers, dots, and hyphens only).',
            ]);
        }

        $inUseByAnotherTenant = Domain::query()
            ->where('domain', $normalizedDomain)
            ->where('tenant_id', '!=', $tenant->id)
            ->exists();

        if ($inUseByAnotherTenant) {
            return back()->withErrors([
                'custom_domain' => 'This domain is already in use by another tenant.',
            ]);
        }

        if (! Domain::query()->where('domain', $normalizedDomain)->where('tenant_id', $tenant->id)->exists()) {
            Domain::query()->create([
                'domain' => $normalizedDomain,
                'tenant_id' => $tenant->id,
            ]);
        }

        $tenant->update([
            'custom_domain' => $normalizedDomain,
        ]);

        $this->updateTenantData($tenant, [
            'custom_domain_status' => 'pending_dns',
            'custom_domain_last_checked_at' => now()->toIso8601String(),
            'domain_onboarding_completed_at' => now()->toIso8601String(),
        ]);

        return back()->with('success', 'Custom domain saved. Point your DNS and mark it verified when ready.');
    }

    private function resolveTenant(Request $request): Tenant
    {
        $currentTenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        if ($currentTenant instanceof Tenant) {
            $tenant = $currentTenant;
        } else {
            $tenant = Tenant::query()->find((string) ($request->user()?->tenant_id ?? ''));
        }

        if (! $tenant) {
            abort(404);
        }

        if ((string) $request->user()->tenant_id !== (string) $tenant->id) {
            abort(403, 'You do not belong to this tenant.');
        }

        return $tenant;
    }

    private function domainStatus(Tenant $tenant): string
    {
        return (string) ($tenant->getAttribute('custom_domain_status') ?? 'not_started');
    }

    private function updateTenantData(Tenant $tenant, array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $tenant->setAttribute($key, $value);
        }

        $tenant->save();
    }
}
