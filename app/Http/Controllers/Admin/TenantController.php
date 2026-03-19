<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::with('domains')->latest()->paginate(50);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:63', 'unique:tenants,slug'],
            'plan' => ['required', 'in:'.implode(',', Tenant::plans())],
            'owner_email' => ['required', 'email'],
        ]);

        $tenant = Tenant::create([
            'id' => $validated['slug'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'plan' => $validated['plan'],
            'owner_email' => $validated['owner_email'],
            'modules_enabled' => Tenant::modulesForPlan($validated['plan']),
            'is_active' => true,
        ]);

        // Assign the subdomain
        $tenant->domains()->create([
            'domain' => $validated['slug'].'.'.config('tenancy.central_domains.3', 'smbgen.com'),
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant \"{$tenant->name}\" created at {$validated['slug']}.smbgen.com");
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted.');
    }
}
