<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\EmailSequence;
use App\Models\ManagedSite;
use App\Models\SocialPost;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenant = tenant();

        $stats = [
            'signal' => $tenant->hasModule('signal') ? [
                'total' => SocialPost::count(),
                'scheduled' => SocialPost::where('status', 'scheduled')->count(),
                'published' => SocialPost::where('status', 'published')->count(),
            ] : null,

            'relay' => $tenant->hasModule('relay') ? [
                'sequences' => EmailSequence::count(),
                'active' => EmailSequence::where('status', 'active')->count(),
            ] : null,

            'surge' => $tenant->hasModule('surge') ? [
                'open_deals' => Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->count(),
                'pipeline' => Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value'),
            ] : null,

            'cast' => $tenant->hasModule('cast') ? [
                'sites' => ManagedSite::count(),
                'live' => ManagedSite::where('status', 'live')->count(),
            ] : null,
        ];

        $modules = $tenant->modules_enabled ?? [];

        return view('tenant.dashboard', compact('tenant', 'stats', 'modules'));
    }
}
