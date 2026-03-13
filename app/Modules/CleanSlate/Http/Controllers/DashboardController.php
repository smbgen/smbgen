<?php

namespace App\Modules\CleanSlate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CleanSlate\Models\Generation;
use App\Modules\CleanSlate\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $tier = $this->subscriptionService->getActiveTier($user);

        $generations = Generation::where('user_id', $user->id)
            ->latest()
            ->get();

        // Count generations used this billing month
        $subscription = $user->subscription('cleanslate');
        $periodStart  = $subscription?->created_at ?? now()->startOfMonth();
        $usedThisMonth = Generation::where('user_id', $user->id)
            ->where('created_at', '>=', $periodStart->startOfMonth())
            ->whereIn('status', ['queued', 'generating', 'complete'])
            ->count();

        $monthlyLimit = match ($tier?->value) {
            'basic'  => 3,
            default  => null, // null = unlimited
        };

        return view('cleanslate::dashboard.index', compact(
            'user', 'tier', 'generations', 'usedThisMonth', 'monthlyLimit'
        ));
    }

    public function download(Request $request, Generation $generation): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        if ($generation->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! $generation->zip_path || ! file_exists(storage_path('app/' . $generation->zip_path))) {
            return back()->with('error', 'Download not available yet.');
        }

        return response()->download(storage_path('app/' . $generation->zip_path));
    }
}
