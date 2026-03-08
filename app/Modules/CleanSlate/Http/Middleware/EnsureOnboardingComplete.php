<?php

namespace App\Modules\CleanSlate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->profile;

        if (! $profile?->onboarding_complete) {
            return redirect()->route('cleanslate.onboarding.profile');
        }

        return $next($request);
    }
}
