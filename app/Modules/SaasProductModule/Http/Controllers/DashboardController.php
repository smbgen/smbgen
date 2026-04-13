<?php

namespace App\Modules\SaasProductModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SaasProductModule\Models\RemovalRequest;
use App\Modules\SaasProductModule\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function index(Request $request): View
    {
        $user    = $request->user();
        $profile = $user->profile;
        $tier    = $this->subscriptionService->getActiveTier($user);

        $scanJobs       = $profile?->scanJobs()->with('dataBroker')->latest()->get() ?? collect();
        $removalRequests = $profile?->removalRequests()->with('dataBroker')->latest()->get() ?? collect();

        return view('saasproductmodule::dashboard.index', compact(
            'profile', 'tier', 'scanJobs', 'removalRequests'
        ));
    }
}
