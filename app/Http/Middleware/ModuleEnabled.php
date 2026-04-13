<?php

namespace App\Http\Middleware;

use App\Support\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        if (! ModuleRegistry::isAvailable($moduleKey) || ! ModuleRegistry::isEnabled($moduleKey) || ! ModuleRegistry::isSelectedFrontend($moduleKey)) {
            abort(404);
        }

        return $next($request);
    }
}
