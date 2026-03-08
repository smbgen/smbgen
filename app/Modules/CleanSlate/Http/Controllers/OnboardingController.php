<?php

namespace App\Modules\CleanSlate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CleanSlate\Jobs\DispatchInitialScanJob;
use App\Modules\CleanSlate\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function profile(Request $request): View
    {
        $profile = $request->user()->profile ?? new Profile();

        return view('cleanslate::onboarding.profile', compact('profile'));
    }

    public function storeProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
        ]);

        Profile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return redirect()->route('cleanslate.onboarding.contact');
    }

    public function contact(Request $request): View
    {
        $profile = $request->user()->profile ?? new Profile();

        return view('cleanslate::onboarding.contact', compact('profile'));
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'emails'   => ['required', 'array', 'min:1'],
            'emails.*' => ['email'],
            'phones'   => ['nullable', 'array'],
            'phones.*' => ['string', 'max:20'],
        ]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['emails' => $data['emails'], 'phones' => $data['phones'] ?? []]
        );

        return redirect()->route('cleanslate.onboarding.addresses');
    }

    public function addresses(Request $request): View
    {
        $profile = $request->user()->profile ?? new Profile();

        return view('cleanslate::onboarding.addresses', compact('profile'));
    }

    public function storeAddresses(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'addresses'             => ['required', 'array', 'min:1'],
            'addresses.*.street'    => ['required', 'string'],
            'addresses.*.city'      => ['required', 'string'],
            'addresses.*.state'     => ['required', 'string', 'size:2'],
            'addresses.*.zip'       => ['required', 'string', 'max:10'],
        ]);

        $request->user()->profile->update(['addresses' => $data['addresses']]);

        return redirect()->route('cleanslate.onboarding.confirm');
    }

    public function confirm(Request $request): View
    {
        $profile = $request->user()->profile;

        return view('cleanslate::onboarding.confirm', compact('profile'));
    }

    public function launch(Request $request): RedirectResponse
    {
        $profile = $request->user()->profile;
        $profile->update(['onboarding_complete' => true]);

        DispatchInitialScanJob::dispatch($request->user());

        return redirect()->route('cleanslate.dashboard')
            ->with('success', 'Scan launched! We\'ll notify you as results come in.');
    }
}
