<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \Illuminate\Auth\AuthServiceProvider::class,
        \Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        \Illuminate\Session\SessionServiceProvider::class,
        \Illuminate\Cookie\CookieServiceProvider::class,
        // EmailEventServiceProvider removed - listeners are auto-discovered in Laravel 12
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'companyAdministrator' => \App\Http\Middleware\CompanyAdministrator::class,
            'cors' => \App\Http\Middleware\HandleCors::class,
        ]);
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
