<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ManagedSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CastController extends Controller
{
    public function index(): View
    {
        $sites = ManagedSite::with('client')->latest()->get();

        $stats = [
            'total' => $sites->count(),
            'active' => $sites->filter(fn ($s) => $s->status->value === 'active')->count(),
            'building' => $sites->filter(fn ($s) => $s->status->value === 'building')->count(),
        ];

        $clients = Client::query()->orderBy('name')->get();

        return view('admin.cast.index', compact('sites', 'stats', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'status' => ['required', 'in:building,active,paused'],
            'notes' => ['nullable', 'string'],
        ]);

        ManagedSite::create($validated);

        return back()->with('success', 'Site added.');
    }

    public function destroy(ManagedSite $site): RedirectResponse
    {
        $site->delete();

        return back()->with('success', 'Site removed.');
    }
}
