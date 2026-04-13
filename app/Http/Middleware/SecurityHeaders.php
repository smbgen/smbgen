<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // In local/development, allow Vite dev server (same host, port 5173) for HMR
        $viteDevSources = '';
        if (config('app.env') !== 'production') {
            $appUrl = rtrim(config('app.url', 'http://localhost'), '/');
            $host = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
            $viteOrigin = "http://{$host}:5173";
            $viteWs = "ws://{$host}:5173";
            $viteDevSources = " {$viteOrigin}";
            $viteDevConnect = " {$viteOrigin} {$viteWs}";
        } else {
            $viteDevConnect = '';
        }

        // Content Security Policy
        $csp = "default-src 'self'; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'{$viteDevSources} https://cdn.tailwindcss.com https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ".
               "style-src 'self' 'unsafe-inline'{$viteDevSources} https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ".
               "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ".
               "img-src 'self' data: https:; ".
               "connect-src 'self' https://api.anthropic.com{$viteDevConnect}; ".
               "frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);

        // HTTP Strict Transport Security (HSTS)
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
