<?php

use Illuminate\Support\Facades\Route;

if (config('app.debug')) {
    Route::prefix('debug')->group(function () {
        Route::get('/error/403', function () {
            return response()->view('errors.403', [], 403);
        })->name('debug.error.403');

        Route::get('/error/404', function () {
            return response()->view('errors.404', [], 404);
        })->name('debug.error.404');

        Route::get('/error/405', function () {
            return response()->view('errors.405', [], 405);
        })->name('debug.error.405');

        Route::get('/error/500', function () {
            $exception = new Exception('This is a test exception for debugging the 500 error page.');

            return response()->view('errors.500', ['exception' => $exception], 500);
        })->name('debug.error.500');

        Route::get('/error/503', function () {
            return response()->view('errors.503', [], 503);
        })->name('debug.error.503');

        Route::get('/test/500', function () {
            throw new Exception('Intentional 500 error for testing');
        })->name('debug.test.500');

        Route::get('/test/403', function () {
            abort(403, 'Intentional 403 error for testing');
        })->name('debug.test.403');

        Route::get('/test/404', function () {
            abort(404, 'Intentional 404 error for testing');
        })->name('debug.test.404');

        Route::get('/test/405', function () {
            abort(405, 'Intentional 405 error for testing');
        })->name('debug.test.405');

        Route::get('/test/503', function () {
            abort(503, 'Intentional 503 error for testing');
        })->name('debug.test.503');

        Route::get('/info', function () {
            return view('debug.info');
        })->name('debug.info');

        Route::get('/design', function () {
            return view('debug.design');
        })->name('debug.design');

        Route::get('/switch-user', function () {
            $usersByRole = \App\Models\User::orderBy('name')
                ->get()
                ->groupBy(function (\App\Models\User $user) {
                    if ($user->isSuperAdmin()) {
                        return 'super_admin';
                    }

                    return $user->role ?? 'unknown';
                });

            return view('debug.switch-user', compact('usersByRole'));
        })->name('debug.switch-user');

        Route::get('/switch-user/{user}', function (\App\Models\User $user) {
            \Illuminate\Support\Facades\Auth::login($user);

            $redirect = match (true) {
                $user->isSuperAdmin() && config('app.super_admin_routes_enabled', false) && Route::has('super-admin.dashboard') => route('super-admin.dashboard'),
                $user->role === 'company_administrator' => route('admin.dashboard'),
                default => route('dashboard'),
            };

            return redirect($redirect)->with('status', "Logged in as {$user->name} ({$user->role})");
        })->name('debug.switch-user.post');
    });
}
