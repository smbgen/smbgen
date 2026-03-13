<?php

use App\Modules\CleanSlate\Http\Controllers\AdminController;
use App\Modules\CleanSlate\Http\Controllers\BillingController;
use App\Modules\CleanSlate\Http\Controllers\DashboardController;
use App\Modules\CleanSlate\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ───────────────────────────────────────────────────────────────────
// 'web' must be explicit — loadRoutesFrom() runs outside the web middleware group
Route::middleware(['web'])->prefix('extreme')->name('cleanslate.')->group(function () {
    Route::get('/plans', [BillingController::class, 'plans'])->name('billing.plans');
});

// ─── CUSTOMER (auth required) ─────────────────────────────────────────────────
Route::middleware(['web', 'auth', 'verified'])->prefix('extreme')->name('cleanslate.')->group(function () {

    // Smart entry — redirects based on subscription/onboarding state
    Route::get('/start', [BillingController::class, 'entry'])->name('entry');

    // Billing
    Route::post('/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');

    // Onboarding (requires active subscription)
    Route::middleware('subscribed')->prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/profile', [OnboardingController::class, 'profile'])->name('profile');
        Route::post('/profile', [OnboardingController::class, 'storeProfile']);
    });

    // Dashboard (requires subscription + completed onboarding)
    Route::middleware(['subscribed', 'onboarding.complete'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/generations/{generation}/download', [DashboardController::class, 'download'])
            ->name('generation.download');
    });
});

// ─── ADMIN ────────────────────────────────────────────────────────────────────
Route::middleware(['web', 'auth', 'verified', 'companyAdministrator'])
    ->prefix('admin/extreme')
    ->name('admin.cleanslate.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
    });
