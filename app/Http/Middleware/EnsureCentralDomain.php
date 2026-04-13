<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) env('TENANCY_ENABLED', false)) {
            return $next($request);
        }

        if (app()->bound('currentTenant')) {
            abort(404);
        }

        $centralDomains = array_filter(array_map(
            static fn ($domain) => strtolower(trim((string) $domain)),
            (array) config('tenancy.central_domains', [])
        ));

        if ($centralDomains === []) {
            return $next($request);
        }

        if (! in_array(strtolower($request->getHost()), $centralDomains, true)) {
            abort(404);
        }

        return $next($request);
    }
}
