<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tenant::query()->with('subscriptionTier');

        if ($request->filled('search')) {
            $search = (string) $request->string('search');

            $query->where(function ($tenantQuery) use ($search) {
                $tenantQuery->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('subdomain', 'like', '%'.$search.'%')
                    ->orWhere('custom_domain', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('plan')) {
            $query->where('plan', (string) $request->string('plan'));
        }

        $tenants = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'billableTenants' => Tenant::query()->whereNotNull('subscription_tier_id')->count(),
            'activeSubscriptions' => Tenant::query()->whereNotNull('stripe_subscription_id')->count(),
            'missingBillingSetup' => Tenant::query()->whereNull('stripe_customer_id')->count(),
            'trialTenants' => Tenant::query()->where('plan', 'trial')->count(),
        ];

        return view('super-admin.billing.index', compact('tenants', 'stats'));
    }
}
