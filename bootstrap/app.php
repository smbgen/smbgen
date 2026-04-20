<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders(array_filter([
        \Illuminate\Auth\AuthServiceProvider::class,
        \Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        \Illuminate\Session\SessionServiceProvider::class,
        \Illuminate\Cookie\CookieServiceProvider::class,
        // EmailEventServiceProvider removed - listeners are auto-discovered in Laravel 12

        // Conditionally load Tenancy Service Provider
        env('TENANCY_ENABLED', false) ? \Stancl\Tenancy\TenancyServiceProvider::class : null,
    ]))
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware
        $middleware->use([
            \App\Http\Middleware\ForceHttps::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'companyAdministrator' => \App\Http\Middleware\CompanyAdministrator::class,
            'superAdmin' => \App\Http\Middleware\SuperAdmin::class,
            'super.admin' => \App\Http\Middleware\SuperAdministrator::class,
            'centralOnly' => \App\Http\Middleware\EnsureCentralDomain::class,
            'tenantOnly' => \App\Http\Middleware\EnsureTenantContext::class,
            'tenantUser' => \App\Http\Middleware\EnsureTenantUserMatchesContext::class,
            'moduleEnabled' => \App\Http\Middleware\ModuleEnabled::class,
            'cors' => \App\Http\Middleware\HandleCors::class,
            'forceHttps' => \App\Http\Middleware\ForceHttps::class,
            'securityHeaders' => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Tenancy middleware group can resolve tenants by domain/subdomain or path.
        $tenancyMiddleware = [];

        if (env('TENANCY_ENABLED', false)) {
            $resolver = strtolower((string) env('TENANCY_RESOLVER', 'domain'));

            $tenancyMiddleware[] = $resolver === 'path'
                ? \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class
                : \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class;
        }

        $middleware->group('tenant', $tenancyMiddleware);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions) {

        $exceptions->context(fn () => [
            'user_id' => (function () {
                try {
                    return function_exists('auth') && app()->bound('auth') ? auth()->id() : null;
                } catch (\Throwable $e) {
                    return null;
                }
            })(),
            'env' => app()->environment(),
        ]);

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => $e->getMessage() ?: 'The requested resource was not found.',
                ], 404);
            }

            return response()->view('errors.404', ['exception' => $e], 404);
        });

        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => $e->getMessage() ?: 'You are not authorized to access this resource.',
                ], 401);
            }

            return response()->view('errors.401', ['exception' => $e], 401);
        });

        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            $expires = $request->query('expires');
            $expiresTimestamp = is_numeric($expires) ? (int) $expires : null;
            $nowTimestamp = now()->timestamp;

            \Illuminate\Support\Facades\Log::info('Invalid verification signature encountered', [
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'host' => $request->getHost(),
                'scheme' => $request->getScheme(),
                'expires_query' => $expires,
                'expires_iso' => $expiresTimestamp ? now()->setTimestamp($expiresTimestamp)->toIso8601String() : null,
                'now_iso' => now()->toIso8601String(),
                'expires_unix' => $expiresTimestamp,
                'now_unix' => $nowTimestamp,
                'seconds_past_expiry' => $expiresTimestamp ? ($nowTimestamp - $expiresTimestamp) : null,
                'app_timezone' => config('app.timezone'),
                'app_url' => config('app.url'),
                'is_authenticated' => auth()->check(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Invalid Signature',
                    'message' => 'This verification link is invalid or expired.',
                ], 403);
            }

            if (auth()->check()) {
                return redirect()->route('verification.notice')
                    ->with('error', 'Verification link is invalid or expired. Please request a new one.');
            }

            return redirect()->route('login')
                ->with('error', 'Verification link is invalid or expired. Please request a new one.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 403) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => $e->getMessage() ?: 'You do not have permission to access this resource.',
                ], 403);
            }

            return response()->view('errors.403', ['exception' => $e], 403);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => $e->getMessage() ?: 'You must be logged in to access this resource.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        });

        $exceptions->respond(function ($response) {
            if ($response instanceof Response && $response->getStatusCode() === 419) {
                return back()->with('message', 'The page expired. Please try again.');
            }

            return $response;
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            // Don't handle ValidationException here - let Laravel handle it normally
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }

            // Send email notification for 500 errors
            if ($e->getCode() >= 500 || $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() >= 500) {
                try {
                    \Illuminate\Support\Facades\Mail::to('alex@oldlinecyber.com')
                        ->send(new \App\Mail\ServerErrorNotification($e, [
                            'url' => $request->fullUrl(),
                            'method' => $request->method(),
                            'user_id' => auth()->id(),
                            'ip' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]));
                } catch (\Throwable $mailError) {
                    // Don't let email failures break error handling
                    \Illuminate\Support\Facades\Log::error('Failed to send error notification email: '.$mailError->getMessage());
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Server Error',
                    'message' => $e->getMessage() ?: 'An unexpected error occurred. Please try again.',
                ], 500);
            }

            return response()->view('errors.500', ['exception' => $e], 500);
        });

        $exceptions->dontReportDuplicates();
    })
    ->create();
