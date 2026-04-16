<?php

use App\Http\Controllers\SuperAdmin\BillingController;
use App\Http\Controllers\SuperAdmin\DeploymentConsoleController;
use App\Http\Controllers\SuperAdmin\DiagnosticsController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['centralOnly', 'auth', 'verified', 'superAdmin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/', [DeploymentConsoleController::class, 'dashboard'])->name('dashboard');
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/deployment-console', [DeploymentConsoleController::class, 'edit'])->name('deployment-console');
        Route::patch('/deployment-console', [DeploymentConsoleController::class, 'update'])->name('deployment-console.update');
        Route::get('/guided-setup', [DeploymentConsoleController::class, 'guidedSetup'])->name('guided-setup');
        Route::post('/guided-setup', [DeploymentConsoleController::class, 'storeGuidedSetup'])->name('guided-setup.store');
        Route::post('/guided-setup/complete', [DeploymentConsoleController::class, 'complete'])->name('guided-setup.complete');
        Route::patch('/users/{user}/super-admin', [DeploymentConsoleController::class, 'updateSuperAdmin'])->name('users.super-admin');
        Route::patch('/users/{user}/tenant', [DeploymentConsoleController::class, 'updateUserTenant'])->name('users.tenant');

        // Tenant management
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
        Route::post('/tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])->name('tenants.impersonate');
        Route::post('/stop-impersonating', [TenantController::class, 'stopImpersonating'])->name('stop-impersonating');
        Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
        Route::post('/tenants/{tenant}/domains', [TenantController::class, 'addDomain'])->name('tenants.domains.add');
        Route::delete('/tenants/{tenant}/domains/{domain}', [TenantController::class, 'removeDomain'])->name('tenants.domains.remove');
        Route::post('/tenants/{tenant}/domains/{domain}/primary', [TenantController::class, 'setPrimaryDomain'])->name('tenants.domains.primary');
        Route::post('/tenants/{tenant}/extend-trial', [TenantController::class, 'extendTrial'])->name('tenants.extend-trial');
        Route::post('/tenants/{tenant}/change-tier', [TenantController::class, 'changeTier'])->name('tenants.change-tier');

        // User management
        Route::post('/tenants/{tenant}/users', [TenantController::class, 'storeUser'])->name('tenants.users.store');
        Route::post('/tenants/{tenant}/users/{user}/verify', [TenantController::class, 'verifyUser'])->name('tenants.users.verify');
        Route::delete('/tenants/{tenant}/users/{user}', [TenantController::class, 'removeUser'])->name('tenants.users.remove');

        // Diagnostics
        Route::get('/diagnostics', [DiagnosticsController::class, 'index'])->name('diagnostics');
        Route::post('/diagnostics/run-migrations', [DiagnosticsController::class, 'runMigrations'])->name('diagnostics.run-migrations');
        Route::post('/diagnostics/clear-caches', [DiagnosticsController::class, 'clearCaches'])->name('diagnostics.clear-caches');
        Route::post('/diagnostics/create-master-tenant', [DiagnosticsController::class, 'createMasterTenant'])->name('diagnostics.create-master-tenant');
        Route::post('/diagnostics/run-tenant-migrations', [DiagnosticsController::class, 'runTenantMigrations'])->name('diagnostics.run-tenant-migrations');
    });
