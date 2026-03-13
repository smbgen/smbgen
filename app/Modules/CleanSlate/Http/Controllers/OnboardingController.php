<?php

namespace App\Modules\CleanSlate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CleanSlate\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function profile(Request $request): View
    {
        return view('cleanslate::onboarding.profile', [
            'user' => $request->user(),
        ]);
    }

    public function storeProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            ['onboarding_complete' => true],
        );

        return redirect()->route('cleanslate.dashboard')
            ->with('success', 'Welcome to Extreme! Start building your first app.');
    }
}
