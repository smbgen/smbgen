<?php

namespace App\Modules\CleanSlate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\CleanSlate\Models\Generation;
use App\Modules\CleanSlate\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function index(): View
    {
        $subscribers = User::withCount('generations')
            ->whereHas('subscriptions', fn ($q) => $q->where('type', 'cleanslate'))
            ->latest()
            ->paginate(25);

        // Attach tier label to each subscriber
        $subscribers->through(function (User $user) {
            $tier = $this->subscriptionService->getActiveTier($user);
            $user->extreme_tier = $tier?->label();
            return $user;
        });

        $stats = [
            'total_subscribers'      => User::whereHas('subscriptions', fn ($q) => $q->where('type', 'cleanslate'))->count(),
            'total_generations'      => Generation::count(),
            'generations_this_month' => Generation::where('created_at', '>=', now()->startOfMonth())->count(),
            'active_subscriptions'   => \Laravel\Cashier\Subscription::where('type', 'cleanslate')->where('stripe_status', 'active')->count(),
        ];

        return view('cleanslate::admin.index', compact('subscribers', 'stats'));
    }
}
