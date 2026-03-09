<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class McpTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('app.mcp_secret');

        if (empty($secret)) {
            return response()->json(['error' => 'MCP not configured'], 503);
        }

        $token = $request->bearerToken();

        if (! $token || ! hash_equals($secret, $token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
