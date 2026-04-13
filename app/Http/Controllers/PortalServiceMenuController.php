<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePortalServicesRequest;
use Illuminate\Http\RedirectResponse;

class PortalServiceMenuController extends Controller
{
    public function update(UpdatePortalServicesRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $enabledServices = array_values(array_unique($validated['enabled_services'] ?? []));

        $request->user()->update([
            'account_tier' => $validated['account_tier'],
            'enabled_services' => $enabledServices,
        ]);

        return back()->with('status', 'Portal services and tier updated.');
    }
}
