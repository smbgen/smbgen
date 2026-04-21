<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantUserMatchesContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.tenancy_enabled', false)) {
            return $next($request);
        }

        if (! auth()->check()) {
            return $next($request);
        }

        if (! app()->bound('currentTenant')) {
            abort(404);
        }

        $user = auth()->user();
        $tenant = app('currentTenant');

        if ($user->isSuperAdmin()) {
            abort(403, 'Super admin access is not allowed directly on tenant routes.');
        }

        if (empty($user->tenant_id) || (string) $user->tenant_id !== (string) $tenant->id) {
            abort(403, 'Your account does not belong to this tenant.');
        }

        return $next($request);
    }
}
