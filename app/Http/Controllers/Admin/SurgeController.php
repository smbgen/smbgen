<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DealStage;
use App\Http\Controllers\Controller;
use App\Jobs\ScoreLeadJob;
use App\Models\Client;
use App\Models\Deal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurgeController extends Controller
{
    public function index(Request $request): View
    {
        $stages = DealStage::cases();

        $dealsByStage = collect($stages)->mapWithKeys(fn ($stage) => [
            $stage->value => Deal::with('client')
                ->where('stage', $stage)
                ->latest()
                ->get(),
        ]);

        $stats = [
            'total_deals' => Deal::count(),
            'pipeline_value' => Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value'),
            'won_value' => Deal::where('stage', DealStage::ClosedWon)->sum('value'),
            'won_count' => Deal::where('stage', DealStage::ClosedWon)->count(),
        ];

        $clients = Client::query()->orderBy('name')->get();

        return view('admin.surge.index', compact('dealsByStage', 'stages', 'stats', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0'],
            'stage' => ['required', 'in:'.implode(',', array_column(DealStage::cases(), 'value'))],
            'notes' => ['nullable', 'string'],
        ]);

        $deal = Deal::create($validated);

        if ($deal->client_id) {
            ScoreLeadJob::dispatch(Client::find($deal->client_id));
        }

        return back()->with('success', 'Deal created.');
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => ['required', 'in:'.implode(',', array_column(DealStage::cases(), 'value'))],
        ]);

        $deal->update($validated);

        return back()->with('success', 'Deal stage updated.');
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $deal->delete();

        return back()->with('success', 'Deal deleted.');
    }
}
