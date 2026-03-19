<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\DashboardController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('tenant.dashboard');

    // Module pages (full controllers to be built out per module)
    Route::get('/signal', fn () => view('tenant.module-coming-soon', ['module' => 'SIGNAL',  'icon' => 'signal']))->name('tenant.signal');
    Route::get('/relay', fn () => view('tenant.module-coming-soon', ['module' => 'RELAY',   'icon' => 'relay']))->name('tenant.relay');
    Route::get('/surge', fn () => view('tenant.module-coming-soon', ['module' => 'SURGE',   'icon' => 'surge']))->name('tenant.surge');
    Route::get('/cast', fn () => view('tenant.module-coming-soon', ['module' => 'CAST',    'icon' => 'cast']))->name('tenant.cast');
    Route::get('/vault', fn () => view('tenant.module-coming-soon', ['module' => 'VAULT',   'icon' => 'vault']))->name('tenant.vault');
    Route::get('/extreme', fn () => view('tenant.module-coming-soon', ['module' => 'EXTREME', 'icon' => 'extreme']))->name('tenant.extreme');
    Route::get('/upgrade', fn () => view('tenant.upgrade'))->name('tenant.upgrade');

    // Profile (re-uses Breeze ProfileController)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('tenant.profile');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('tenant.profile.update');
});
