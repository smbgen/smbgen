<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgencyPortal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function index(): View
    {
        $portals = AgencyPortal::with(['owner', 'sites'])->latest()->get();

        $stats = [
            'total' => $portals->count(),
            'active' => $portals->where('status', 'active')->count(),
            'sites' => $portals->sum(fn ($p) => $p->sites->count()),
        ];

        return view('admin.agency.index', compact('portals', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'max_client_sites' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        AgencyPortal::create([
            'owner_user_id' => auth()->id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'max_client_sites' => $validated['max_client_sites'],
            'status' => 'active',
        ]);

        return back()->with('success', 'Agency portal created.');
    }

    public function destroy(AgencyPortal $portal): RedirectResponse
    {
        $portal->delete();

        return back()->with('success', 'Portal deleted.');
    }
}
