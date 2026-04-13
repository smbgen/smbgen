<?php

use App\Http\Controllers\AISEOController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientFileController;
use App\Http\Controllers\CyberAuditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PortalServiceMenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        $messages = \App\Models\Message::with(['sender', 'recipient'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $unreadCount = \App\Models\Message::query()
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->count();

        $recentInvoices = collect();

        if (config('business.features.billing')) {
            $recentInvoices = \App\Models\Invoice::query()
                ->where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get();
        }

        $workspace = null;

        if (app()->bound('currentTenant')) {
            $tenant = app('currentTenant');

            $workspace = [
                'name' => $tenant->name,
                'subdomain' => $tenant->subdomain,
                'customDomain' => $tenant->custom_domain,
                'domainStatus' => (string) ($tenant->getAttribute('custom_domain_status') ?? 'not_started'),
            ];
        }

        return view('dashboard', compact('messages', 'unreadCount', 'recentInvoices', 'workspace'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/client/seo-assistant', [AISEOController::class, 'viewTool'])->name('client.seo-assistant');
    Route::post('/client/seo-assistant/query', [AISEOController::class, 'handleQuery'])->name('client.seo-assistant.query');

    Route::get('/client/cyber-audit', [CyberAuditController::class, 'index'])->name('cyber-audit.index');
    Route::post('/client/cyber-audit/chat', [CyberAuditController::class, 'chat'])->name('cyber-audit.chat');
    Route::post('/client/cyber-audit/clear-history', [CyberAuditController::class, 'clearHistory'])->name('cyber-audit.clear-history');

    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

    Route::patch('/portal/service-menu', [PortalServiceMenuController::class, 'update'])
        ->name('portal.service-menu.update');

    if (config('business.features.billing')) {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::post('/billing/invoices/{invoice}/pay', [BillingController::class, 'pay'])->name('billing.pay');
    }

    Route::get('/documents', [ClientFileController::class, 'index'])->name('client.files');
    Route::post('/documents/upload', [ClientFileController::class, 'store'])
        ->middleware('throttle:8,1')
        ->name('client.files.upload');
    Route::get('/documents/download/{file}', [ClientFileController::class, 'download'])->name('client.files.download');
    Route::delete('/documents/{file}', [ClientFileController::class, 'destroy'])->name('client.files.destroy');

    if (config('business.features.inspection_reports', false)) {
        Route::get('/reports/{report}', [\App\Http\Controllers\Admin\InspectionReportController::class, 'show'])->name('reports.show');
    }
});
