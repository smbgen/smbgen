<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.tenancy_enabled', false)) {
            return $next($request);
        }

        if (! app()->bound('currentTenant')) {
            abort(404);
        }

        return $next($request);
    }
}
