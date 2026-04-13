<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovedIP
{
    public function handle(Request $request, Closure $next): Response
    {
        $approvedIps = config('approved_ips');
        $clientIp = $request->ip();

        if (! in_array($clientIp, $approvedIps)) {
            return response()->view('errors.denied-ip', ['ip' => $clientIp]);
        }

        return $next($request);
    }
}
