<?php

namespace App\Modules\SaasProductModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SaasProductModule\Models\DataBroker;
use App\Modules\SaasProductModule\Models\Profile;
use App\Modules\SaasProductModule\Models\RemovalRequest;
use App\Modules\SaasProductModule\Models\ScanJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $profiles = Profile::with(['user', 'scanJobs', 'removalRequests'])
            ->latest()
            ->paginate(25);

        $stats = [
            'total_customers'   => Profile::count(),
            'active_scans'      => \App\Modules\SaasProductModule\Models\ScanJob::whereIn('status', ['pending', 'running'])->count(),
            'pending_removals'  => \App\Modules\SaasProductModule\Models\RemovalRequest::where('status', 'pending')->count(),
            'confirmed_removals'=> \App\Modules\SaasProductModule\Models\RemovalRequest::where('status', 'confirmed')->count(),
        ];

        return view('saasproductmodule::admin.index', compact('profiles', 'stats'));
    }

    public function debug(): View
    {
        // Collect all SaaS Product Module routes
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => str_starts_with($r->getName() ?? '', 'saasproductmodule.') || str_starts_with($r->getName() ?? '', 'admin.saasproductmodule.') || $r->getName() === 'saas-product-module' || $r->getName() === 'saas-product-module.intake')
            ->map(fn ($r) => [
                'name'       => $r->getName(),
                'methods'    => implode('|', $r->methods()),
                'uri'        => $r->uri(),
                'middleware' => implode(', ', $r->gatherMiddleware()),
                'action'     => $r->getActionName(),
            ])
            ->values();

        $dbStats = [
            'profiles'         => Profile::count(),
            'brokers_total'    => DataBroker::count(),
            'brokers_active'   => DataBroker::where('active', true)->count(),
            'scan_jobs'        => ScanJob::count(),
            'removal_requests' => RemovalRequest::count(),
        ];

        $brokersByTier = DataBroker::orderBy('name')->get()->groupBy('tier');

        $envKeys = [
            'CLEANSLATE_STRIPE_PRICE_BASIC',
            'CLEANSLATE_STRIPE_PRICE_PROFESSIONAL',
            'CLEANSLATE_STRIPE_PRICE_EXECUTIVE',
            'STRIPE_KEY',
            'STRIPE_SECRET',
            'CASHIER_MODEL',
        ];
        $envValues = collect($envKeys)->mapWithKeys(fn ($k) => [
            $k => env($k) ? (str_contains($k, 'SECRET') || str_contains($k, 'KEY') ? substr(env($k), 0, 8) . '…' : env($k)) : null,
        ]);

        return view('saasproductmodule::admin.debug', compact('routes', 'dbStats', 'brokersByTier', 'envValues'));
    }

    public function show(Profile $profile): View
    {
        $profile->load([
            'user',
            'scanJobs.dataBroker',
            'removalRequests.dataBroker',
        ]);

        return view('saasproductmodule::admin.show', compact('profile'));
    }

    public function brokers(): View
    {
        $brokers = DataBroker::orderBy('tier')->orderBy('name')->get();

        return view('saasproductmodule::admin.brokers', compact('brokers'));
    }

    public function updateBroker(Request $request, DataBroker $broker): RedirectResponse
    {
        $request->validate([
            'active' => ['required', 'boolean'],
            'tier'      => ['required', 'integer', 'min:1', 'max:3'],
        ]);

        $broker->update($request->only('active', 'tier'));

        return back()->with('success', "Updated {$broker->name}.");
    }
}
