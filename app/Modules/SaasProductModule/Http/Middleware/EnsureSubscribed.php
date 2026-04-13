<?php

namespace App\Modules\SaasProductModule\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->subscribed('saasproductmodule')) {
            return redirect()->route('saasproductmodule.billing.plans')
                ->with('error', 'An active subscription is required to access this area.');
        }

        return $next($request);
    }
}
