<?php

use App\Http\Controllers\Admin\AdminBillingController;
use App\Http\Controllers\Admin\AdminClientFileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WordPressImportController;
use App\Http\Controllers\AISEOController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientFileController;
use App\Http\Controllers\CmsFormSubmissionController;
use App\Http\Controllers\CmsPagePublicController;
use App\Http\Controllers\CyberAuditController;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\LeadFormController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Email tracking routes (public - no auth required, work on all domains)
Route::get('/track/email/{id}', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('/track/click/{id}', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');

// Public file serving routes (work on all domains)
Route::get('/assets/{path}', function (string $path) {
    // Only serve files from allowed directories
    if (! str_starts_with($path, 'user_files/') && ! str_starts_with($path, 'client_files/') && ! str_starts_with($path, 'cms/images/')) {
        abort(404);
    }

    $disk = Storage::disk('public_cloud');
    $diskConfig = config('filesystems.disks.public_cloud');

    // For S3/R2 (often private endpoints), redirect to a short-lived signed URL.
    if (($diskConfig['driver'] ?? null) === 's3') {
        try {
            $signedUrl = $disk->temporaryUrl($path, now()->addMinutes(10));

            return redirect($signedUrl, 302)
                ->header('Cache-Control', 'private, no-store');
        } catch (\Throwable $e) {
            \Log::error('[ASSETS] Failed to generate temporary URL', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            abort(404);
        }
    }

    // For local storage, check if file exists and stream it
    try {
        if ($disk->exists($path)) {
            return $disk->response($path);
        }
    } catch (\Throwable $e) {
        \Log::error('[ASSETS] Exception accessing file', [
            'path' => $path,
            'error' => $e->getMessage(),
        ]);
    }

    // Check public disk as fallback
    try {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }
    } catch (\Throwable $e) {
        // Silently fail fallback
    }

    abort(404);
})->where('path', '.*')->name('assets.public');

Route::get('/storage/{path}', function ($path) {
    // Backward compatible: keep old /storage links working.
    return redirect(url('/assets/'.ltrim($path, '/')), 301);

    // Only serve files from allowed directories
    if (! str_starts_with($path, 'user_files/') && ! str_starts_with($path, 'client_files/') && ! str_starts_with($path, 'cms/images/')) {
        abort(404);
    }

    $disk = Storage::disk('public_cloud');
    $diskConfig = config('filesystems.disks.public_cloud');

    // For S3/cloud storage with public visibility, use the public URL directly
    if (isset($diskConfig['driver']) && $diskConfig['driver'] === 's3') {
        try {
            // Get the public URL from the disk (Laravel automatically handles URL generation for S3)
            $publicUrl = $disk->url($path);

            // Redirect to the S3 public URL
            return redirect($publicUrl, 301);
        } catch (\Exception $e) {
            \Log::error('[STORAGE] Failed to generate S3 URL', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            abort(404);
        }
    }

    // For local storage, check if file exists and stream it
    try {
        if ($disk->exists($path)) {
            return $disk->response($path);
        }
    } catch (\Exception $e) {
        \Log::error('[STORAGE] Exception accessing file', [
            'path' => $path,
            'error' => $e->getMessage(),
        ]);
    }

    // Check public disk as fallback
    try {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }
    } catch (\Exception $e) {
        // Silently fail fallback
    }

    abort(404);
})->where('path', '.*')->name('storage.public');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

// Legal pages
Route::view('/eula', 'legal.eula')->name('legal.eula');
Route::view('/privacy', 'legal.privacy')->name('legal.privacy');

/*
|--------------------------------------------------------------------------
| Homepage Route Logic
|--------------------------------------------------------------------------
|
| The homepage follows a priority-based routing system:
|
| 1. CMS Override (Highest Priority)
|    - If CMS is enabled AND a published CMS page with slug 'home' exists,
|      it will be displayed as the homepage
|    - This allows full control of the homepage through the CMS admin panel
|
| 2. Landing Page (Default Public View)
|    - If no CMS home page exists and user is not authenticated,
|      displays the static landing.blade.php template
|    - Used for marketing, lead generation, or public-facing content
|
| 3. Dashboard Redirect (Authenticated Users)
|    - Logged-in users are automatically redirected to their dashboard:
|      * Company administrators → admin.dashboard
|      * Regular users/clients → dashboard
|
| This approach provides flexibility between static landing pages,
| dynamic CMS content, and authenticated user experiences.
|
*/
Route::get('/', function () {
    // Try to load CMS home page if CMS is enabled
    if (config('business.features.cms')) {
        $landingPage = \App\Models\CmsPage::where('slug', 'home')
            ->where('is_published', true)
            ->first();

        if ($landingPage) {
            return view('cms.show', ['page' => $landingPage]);
        }
    }

    // Redirect non-authenticated users to login
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    // Redirect authenticated users to their dashboards
    return auth()->user()->role === 'company_administrator'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
});

// Contact page - CMS overridable, fallback to built-in contact page
Route::get('/contact', function () {
    // Check if CMS contact page exists
    if (config('business.features.cms')) {
        $contactPage = \App\Models\CmsPage::where('slug', 'contact')
            ->where('is_published', true)
            ->first();

        if ($contactPage) {
            return view('cms.show', ['page' => $contactPage]);
        }
    }

    // Fallback to built-in contact page
    return view('contact');
})->name('contact');

Route::post('/contact', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// booking feature public route
if (config('business.features.booking')) {
    Route::get('/book', [BookingController::class, 'showWizard'])->name('booking.wizard');
    Route::get('/book/availability', [BookingController::class, 'availability'])->name('booking.availability');
    Route::post('/book', [BookingController::class, 'book'])->name('booking.book');
    Route::get('/book/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
}

/*
|--------------------------------------------------------------------------
| Auth Routes (Login/Register/Google)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

// Note: Livewire demo route removed

Route::get('/auth/google/redirect', [AuthenticatedSessionController::class, 'redirectToGoogle'])
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Client Portal)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        // Get recent messages for the user (last 24 hours)
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

        return view('dashboard', compact('messages'));
    })->name('dashboard');

    // Profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AI SEO Assistant
    Route::get('/client/seo-assistant', [AISEOController::class, 'viewTool'])->name('client.seo-assistant');
    Route::post('/client/seo-assistant/query', [AISEOController::class, 'handleQuery'])->name('client.seo-assistant.query');

    // Cyber Audit Assistant
    Route::get('/client/cyber-audit', [CyberAuditController::class, 'index'])->name('cyber-audit.index');
    Route::post('/client/cyber-audit/chat', [CyberAuditController::class, 'chat'])->name('cyber-audit.chat');
    Route::post('/client/cyber-audit/clear-history', [CyberAuditController::class, 'clearHistory'])->name('cyber-audit.clear-history');

    // Stripe Payment routes
    Route::post('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

    // Billing
    if (config('business.features.billing')) {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::post('/billing/invoices/{invoice}/pay', [BillingController::class, 'pay'])->name('billing.pay');
    }

    // Client Files
    Route::get('/documents', [ClientFileController::class, 'index'])->name('client.files');
    Route::post('/documents/upload', [ClientFileController::class, 'store'])
        ->middleware('throttle:8,1')
        ->name('client.files.upload');
    Route::get('/documents/download/{file}', [ClientFileController::class, 'download'])->name('client.files.download');
    Route::delete('/documents/{file}', [ClientFileController::class, 'destroy'])->name('client.files.destroy');

    // View an inspection report in the client portal
    if (config('business.features.inspection_reports', false)) {
        Route::get('/reports/{report}', [\App\Http\Controllers\Admin\InspectionReportController::class, 'show'])->name('reports.show');
    }

});

// Messages - Available to all authenticated users (no email verification required)
Route::middleware(['auth'])->group(function () {
    Route::resource('messages', MessageController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
});

// Stripe Webhook routes (no auth required)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

// Simple payment collection (public)
Route::get('/pay', [PaymentController::class, 'collect'])->name('payment.collect');
Route::post('/pay/process', [PaymentController::class, 'process'])->name('payment.process');
Route::post('/pay/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');

// /status.json endpoint removed — status is no longer publicly exposed.

// Public Booking Routes
Route::get('/book', [BookingController::class, 'showWizard'])->name('booking.wizard');
Route::get('/book/availability', [BookingController::class, 'availability'])->name('booking.availability');
Route::post('/book', [BookingController::class, 'book'])->name('booking.book');
Route::get('/book/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');

/*
|--------------------------------------------------------------------------
| Admin Routes (companyAdministrator role)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'companyAdministrator'])->prefix('admin')->group(function () {

    // Admin Dashboard - Main admin view
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    // Legacy admin route (redirects to dashboard)
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin');

    Route::get('/test', function () {
        return view('admin.test');
    })->name('admin.test');

    // Search functionality
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'search'])->name('admin.search');
    Route::get('/search/stats', [\App\Http\Controllers\Admin\SearchController::class, 'stats'])->name('admin.search.stats');

    // Lead Management
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('admin.leads.index');
    Route::get('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('admin.leads.show');
    Route::post('/leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('admin.leads.convert');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('admin.leads.destroy');
    Route::get('/leads/export/csv', [\App\Http\Controllers\Admin\LeadController::class, 'exportCsv'])->name('admin.leads.export.csv');
    Route::post('/leads/toggle-notifications', [\App\Http\Controllers\Admin\LeadController::class, 'toggleNotifications'])->name('admin.leads.toggle-notifications');

    // Inspection report create
    if (config('business.features.inspection_reports', false)) {
        Route::post('/inspection-reports', [\App\Http\Controllers\Admin\InspectionReportController::class, 'store'])->name('admin.inspection-reports.store');
        Route::get('/inspection-reports', [\App\Http\Controllers\Admin\InspectionReportController::class, 'index'])->name('admin.inspection-reports.index');
        Route::get('/inspection-reports/create', [\App\Http\Controllers\Admin\InspectionReportController::class, 'create'])->name('admin.inspection-reports.create');
        Route::get('/inspection-reports/{report}', [\App\Http\Controllers\Admin\InspectionReportController::class, 'show'])->name('admin.inspection-reports.show');
        Route::post('/inspection-reports/{report}/resend', [\App\Http\Controllers\Admin\InspectionReportController::class, 'resend'])->name('admin.inspection-reports.resend');
        Route::post('/inspection-reports/{report}/store-to-drive', [\App\Http\Controllers\Admin\InspectionReportController::class, 'storeToGoogleDrive'])->name('admin.inspection-reports.store-to-drive');
    }

    // Legacy lead routes (keeping for backward compatibility)
    Route::post('/leads/{lead}/convert-legacy', [LeadFormController::class, 'convert'])->name('leads.convert');
    Route::get('/leads/partial', [LeadFormController::class, 'partial'])->name('leads.partial');

    // Export clients to CSV
    Route::get('/clients/export/csv', [ClientController::class, 'exportCsv'])->name('clients.export.csv');

    // User Management
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])
        ->name('admin.users.updatePassword');

    // Easter egg?
    Route::get('/game', function () {
        return view('admin.game');
    })->name('admin.game');

    // Admin Client Files - MUST be before resource route to prevent conflicts
    Route::get('/clients/files', [AdminClientFileController::class, 'all'])->name('admin.clients.files.overview');
    Route::get('/clients/{client}/files', [AdminClientFileController::class, 'index'])->name('admin.client.files');
    Route::post('/clients/{client}/files', [AdminClientFileController::class, 'store'])->name('admin.client.files.upload');
    Route::delete('/clients/{client}/files/{file}', [AdminClientFileController::class, 'destroy'])->name('admin.client.files.destroy');
    Route::get('/clients/{client}/files/{file}/download', [AdminClientFileController::class, 'download'])->name('admin.client.files.download');

    // Admin User Files (files not associated with clients)
    Route::get('/users/{user}/files', [AdminClientFileController::class, 'userFiles'])->name('admin.user.files');
    Route::post('/users/{user}/files', [AdminClientFileController::class, 'storeUserFile'])->name('admin.user.files.upload');
    Route::delete('/users/{user}/files/{file}', [AdminClientFileController::class, 'destroyUserFile'])->name('admin.user.files.destroy');
    Route::get('/users/{user}/files/{file}/download', [AdminClientFileController::class, 'downloadUserFile'])->name('admin.user.files.download');

    // Client management
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/provision', [ClientController::class, 'provision'])
        ->name('clients.provision');
    Route::post('/clients/{client}/link-google', [ClientController::class, 'linkGoogleId'])
        ->name('clients.link-google');
    Route::patch('/clients/{client}/toggle-access', [ClientController::class, 'toggleAccess'])
        ->name('clients.toggle-access');

    // Client Import
    Route::get('/clients-import', [\App\Http\Controllers\ClientImportController::class, 'index'])->name('clients.import.index');
    Route::post('/clients-import/upload', [\App\Http\Controllers\ClientImportController::class, 'upload'])->name('clients.import.upload');
    Route::get('/clients-import/{clientImport}/preview', [\App\Http\Controllers\ClientImportController::class, 'preview'])->name('clients.import.preview');
    Route::post('/clients-import/{clientImport}/process', [\App\Http\Controllers\ClientImportController::class, 'process'])->name('clients.import.process');
    Route::get('/clients-import/history', [\App\Http\Controllers\ClientImportController::class, 'history'])->name('clients.import.history');

    // Billing Admin
    if (config('business.features.billing')) {
        Route::get('/billing', [AdminBillingController::class, 'index'])->name('admin.billing.index');
        Route::get('/billing/create', [AdminBillingController::class, 'create'])->name('admin.billing.create');
        Route::get('/billing/{user}', [AdminBillingController::class, 'show'])->name('admin.billing.show');
        Route::get('/billing/{user}/create', [AdminBillingController::class, 'create'])->name('admin.billing.create.with-user');
        Route::post('/billing/{user?}', [AdminBillingController::class, 'store'])->name('admin.billing.store');
        Route::post('/billing/invoices/{invoice}/send', [AdminBillingController::class, 'sendInvoice'])->name('admin.billing.invoices.send');

        Route::post('/billing/invoices/{invoice}/sync-and-send', [AdminBillingController::class, 'syncAndSendInvoice'])->name('admin.billing.invoices.sync-and-send');
        Route::post('/billing/invoices/{invoice}/stripe-payment-link', [AdminBillingController::class, 'generateStripePaymentLink'])->name('admin.billing.invoices.stripe-payment-link');
        Route::post('/billing/invoices/{invoice}/send-stripe', [AdminBillingController::class, 'sendStripeInvoice'])->name('admin.billing.invoices.send-stripe');
        Route::post('/billing/invoices/{invoice}/refund', [AdminBillingController::class, 'refundPayment'])->name('admin.billing.invoices.refund');
        Route::delete('/billing/invoices/{invoice}', [AdminBillingController::class, 'destroy'])->name('admin.billing.invoices.destroy');
    }

    // Business Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\BusinessSettingsController::class, 'index'])->name('admin.business_settings.index');
    Route::patch('/settings', [\App\Http\Controllers\Admin\BusinessSettingsController::class, 'update'])->name('admin.business_settings.update');

    // Setup Wizard
    Route::get('/setup-wizard', [\App\Http\Controllers\Admin\SetupWizardController::class, 'index'])->name('admin.setup-wizard.index');
    Route::get('/setup-wizard/{step}', [\App\Http\Controllers\Admin\SetupWizardController::class, 'show'])->name('admin.setup-wizard.show');
    Route::post('/setup-wizard/business', [\App\Http\Controllers\Admin\SetupWizardController::class, 'saveBusiness'])->name('admin.setup-wizard.business');
    Route::post('/setup-wizard/theme', [\App\Http\Controllers\Admin\SetupWizardController::class, 'saveTheme'])->name('admin.setup-wizard.theme');
    Route::post('/setup-wizard/first-page', [\App\Http\Controllers\Admin\SetupWizardController::class, 'generateFirstPage'])->name('admin.setup-wizard.first-page');
    Route::post('/setup-wizard/skip', [\App\Http\Controllers\Admin\SetupWizardController::class, 'skipStep'])->name('admin.setup-wizard.skip');
    Route::post('/setup-wizard/complete', [\App\Http\Controllers\Admin\SetupWizardController::class, 'complete'])->name('admin.setup-wizard.complete');
    Route::post('/setup-wizard/dismiss', [\App\Http\Controllers\Admin\SetupWizardController::class, 'dismiss'])->name('admin.setup-wizard.dismiss');

    // Environment Settings
    Route::get('/environment-settings', [\App\Http\Controllers\Admin\EnvironmentSettingsController::class, 'index'])->name('admin.environment_settings.index');
    Route::patch('/environment-settings', [\App\Http\Controllers\Admin\EnvironmentSettingsController::class, 'update'])->name('admin.environment_settings.update');

    // Google OAuth Management
    Route::get('/google-oauth', [AdminDashboardController::class, 'googleOAuth'])->name('admin.google-oauth');

    // Email Debug (only available when APP_DEBUG=true)
    if (config('app.debug')) {
        Route::post('/email/test', [AdminDashboardController::class, 'sendTestEmail'])->name('admin.email.test');
    }

    // Email Composer
    Route::get('/email', [\App\Http\Controllers\Admin\EmailController::class, 'index'])->name('admin.email.index');
    Route::post('/email/send', [\App\Http\Controllers\Admin\EmailController::class, 'send'])->name('admin.email.send');
    Route::get('/email/template', [\App\Http\Controllers\Admin\EmailController::class, 'getTemplate'])->name('admin.email.template');
    Route::get('/email/booking-emails', [\App\Http\Controllers\Admin\EmailController::class, 'getBookingEmails'])->name('admin.email.booking-emails');
    Route::get('/email/all-emails', [\App\Http\Controllers\Admin\EmailController::class, 'getAllEmails'])->name('admin.email.all-emails');

    // Availability Management
    Route::resource('availability', \App\Http\Controllers\Admin\AvailabilityController::class)->names([
        'index' => 'admin.availability.index',
        'create' => 'admin.availability.create',
        'store' => 'admin.availability.store',
        'edit' => 'admin.availability.edit',
        'update' => 'admin.availability.update',
        'destroy' => 'admin.availability.destroy',
    ]);

    // Blackout Dates
    Route::post('/availability/blackout', [\App\Http\Controllers\Admin\AvailabilityController::class, 'storeBlackout'])->name('admin.availability.blackout.store');
    Route::delete('/availability/blackout/{blackoutDate}', [\App\Http\Controllers\Admin\AvailabilityController::class, 'destroyBlackout'])->name('admin.availability.blackout.destroy');

    // CMS Pages (feature flag controlled)
    if (config('business.features.cms')) {
        // CMS Images - Custom routes MUST be defined before resource routes
        Route::delete('/cms/images/bulk-delete', [\App\Http\Controllers\Admin\CmsImageController::class, 'bulkDelete'])->name('admin.cms.images.bulk-delete');
        Route::get('/cms/images-api', [\App\Http\Controllers\Admin\CmsImageController::class, 'apiList'])->name('admin.cms.images.api');
        Route::get('/cms/images-api/recent', [\App\Http\Controllers\Admin\CmsImageController::class, 'apiRecent'])->name('admin.cms.images.api.recent');

        Route::resource('cms/images', \App\Http\Controllers\Admin\CmsImageController::class)->names([
            'index' => 'admin.cms.images.index',
            'create' => 'admin.cms.images.create',
            'store' => 'admin.cms.images.store',
            'show' => 'admin.cms.images.show',
            'edit' => 'admin.cms.images.edit',
            'update' => 'admin.cms.images.update',
            'destroy' => 'admin.cms.images.destroy',
        ]);

        // CMS Pages - defined after cms/images to avoid conflicting with {cmsPage} parameter
        Route::post('/cms/navbar', [\App\Http\Controllers\Admin\CmsPageController::class, 'updateNavbar'])->name('admin.cms.navbar.update');
        Route::post('/cms/footer', [\App\Http\Controllers\Admin\CmsPageController::class, 'updateFooter'])->name('admin.cms.footer.update');
        Route::post('/cms/colors', [\App\Http\Controllers\Admin\CmsPageController::class, 'updateCompanyColors'])->name('admin.cms.colors.update');
        Route::get('/cms/default-css-classes', [\App\Http\Controllers\Admin\CmsPageController::class, 'getDefaultCssClasses'])->name('admin.cms.default-css-classes');
        Route::post('/cms/{cmsPage}/duplicate', [\App\Http\Controllers\Admin\CmsPageController::class, 'duplicate'])->name('admin.cms.duplicate');
        Route::resource('cms', \App\Http\Controllers\Admin\CmsPageController::class)->names([
            'index' => 'admin.cms.index',
            'create' => 'admin.cms.create',
            'store' => 'admin.cms.store',
            'show' => 'admin.cms.show',
            'edit' => 'admin.cms.edit',
            'update' => 'admin.cms.update',
            'destroy' => 'admin.cms.destroy',
        ])->parameters([
            'cms' => 'cmsPage',
        ]);
    }

    // Bookings Management
    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/bookings/dashboard', [\App\Http\Controllers\Admin\BookingController::class, 'dashboard'])->name('admin.bookings.dashboard');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('/bookings/{booking}/convert-to-client', [\App\Http\Controllers\Admin\BookingController::class, 'convertToClient'])->name('admin.bookings.convert-to-client');
    Route::post('/bookings/{booking}/send-reminder', [\App\Http\Controllers\Admin\BookingController::class, 'sendReminder'])->name('admin.bookings.send-reminder');
    Route::delete('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('admin.bookings.destroy');

    // Booking Form Configuration
    Route::get('/booking-fields', [\App\Http\Controllers\Admin\BookingFieldConfigController::class, 'edit'])->name('admin.booking-fields.edit');
    Route::put('/booking-fields', [\App\Http\Controllers\Admin\BookingFieldConfigController::class, 'update'])->name('admin.booking-fields.update');

    // Email Deliverability Logs
    Route::get('/email-logs', [\App\Http\Controllers\Admin\EmailLogController::class, 'index'])->name('admin.email-logs.index');
    Route::get('/email-logs/{emailLog}', [\App\Http\Controllers\Admin\EmailLogController::class, 'show'])->name('admin.email-logs.show');
    Route::post('/email-logs/{emailLog}/resend', [\App\Http\Controllers\Admin\EmailLogController::class, 'resend'])->name('admin.email-logs.resend');
    Route::delete('/email-logs/{emailLog}', [\App\Http\Controllers\Admin\EmailLogController::class, 'destroy'])->name('admin.email-logs.destroy');
    Route::post('/email-logs/test-smtp', [\App\Http\Controllers\Admin\EmailLogController::class, 'testSmtp'])->name('admin.email-logs.test-smtp');

    // Internal Docs Browser
    Route::get('/docs', [\App\Http\Controllers\Admin\AdminDocsController::class, 'index'])->name('admin.docs.index');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('admin.activity-logs.show');
    Route::delete('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'destroy'])->name('admin.activity-logs.destroy');
    Route::delete('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('admin.activity-logs.clear');

    Route::post('/google-oauth', function (Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'external_account_email' => 'required|email',
            'calendar_id' => 'nullable|string',
        ]);

        \App\Models\GoogleCredential::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'external_account_email' => $request->external_account_email,
                'calendar_id' => $request->calendar_id,
            ]
        );

        return back()->with('status', 'Google OAuth credentials linked successfully.');
    })->name('admin.google-oauth.store');

    // Admin: send magic link to a user (single-use login)
    Route::post('/users/{user}/magic-link/send', [\App\Http\Controllers\MagicLinkController::class, 'send'])
        ->name('admin.users.magiclink.send');

    // Calendar connect for appointments feature
    Route::get('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('admin.calendar.index');
    Route::get('/calendar/redirect', [\App\Http\Controllers\Admin\CalendarController::class, 'redirectToGoogle'])->name('admin.calendar.redirect');
    Route::get('/calendar/connect', [\App\Http\Controllers\Admin\CalendarController::class, 'redirectToGoogle'])->name('admin.calendar.connect');
    Route::get('/calendar/callback', [\App\Http\Controllers\Admin\CalendarController::class, 'handleGoogleCallback'])->name('admin.calendar.callback');
    Route::get('/calendar/select', [\App\Http\Controllers\Admin\CalendarController::class, 'selectCalendar'])->name('admin.calendar.select');
    Route::post('/calendar/update', [\App\Http\Controllers\Admin\CalendarController::class, 'updateCalendar'])->name('admin.calendar.update');
    Route::post('/calendar/disconnect', [\App\Http\Controllers\Admin\CalendarController::class, 'disconnect'])->name('admin.calendar.disconnect');

    // Elevate a user to company administrator
    Route::post('/users/{user}/elevate', [\App\Http\Controllers\Admin\UserController::class, 'elevate'])
        ->name('admin.users.elevate');

    // Verify/Unverify user email manually
    Route::post('/users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])
        ->name('admin.users.verify');
    Route::post('/users/{user}/unverify', [\App\Http\Controllers\Admin\UserController::class, 'unverify'])
        ->name('admin.users.unverify');

    // Presentations — Packages
    Route::get('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'index'])
        ->name('admin.packages.index');
    Route::get('/packages/create', [\App\Http\Controllers\Admin\PackageController::class, 'create'])
        ->name('admin.packages.create');
    Route::post('/packages/review', [\App\Http\Controllers\Admin\PackageController::class, 'review'])
        ->name('admin.packages.review');
    Route::post('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'store'])
        ->name('admin.packages.store');
    Route::get('/packages/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'show'])
        ->name('admin.packages.show');
    Route::patch('/packages/{package}/status', [\App\Http\Controllers\Admin\PackageController::class, 'updateStatus'])
        ->name('admin.packages.status');
    Route::patch('/packages/{package}/toggle-portal', [\App\Http\Controllers\Admin\PackageController::class, 'togglePortal'])
        ->name('admin.packages.toggle-portal');
    Route::patch('/packages/{package}/files/{file}/promote', [\App\Http\Controllers\Admin\PackageController::class, 'togglePromote'])
        ->name('admin.packages.files.promote');
    Route::get('/packages/{package}/files/{file}/preview', [\App\Http\Controllers\Admin\PackageController::class, 'previewFile'])
        ->name('admin.packages.files.preview');
    Route::get('/packages/{package}/files/{file}/content', [\App\Http\Controllers\Admin\PackageController::class, 'fileContent'])
        ->name('admin.packages.files.content');
});

// Public consume route for magic links
Route::get('/magic-link/{token}/consume', [\App\Http\Controllers\MagicLinkController::class, 'consume'])
    ->middleware('throttle:10,1')
    ->name('magic.consume');

/*
|--------------------------------------------------------------------------
| Debug Routes (Only available when APP_DEBUG=true)
|--------------------------------------------------------------------------
*/

if (config('app.debug')) {
    Route::prefix('debug')->group(function () {
        // Error Page Testing Routes
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
            // Create a mock exception for testing
            $exception = new Exception('This is a test exception for debugging the 500 error page.');

            return response()->view('errors.500', ['exception' => $exception], 500);
        })->name('debug.error.500');

        Route::get('/error/503', function () {
            return response()->view('errors.503', [], 503);
        })->name('debug.error.503');

        // Force actual errors for testing
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

        // Debug info route
        Route::get('/info', function () {
            return view('debug.info');
        })->name('debug.info');

        // Design Playground (Local Development Only)
        Route::get('/design', function () {
            return view('debug.design');
        })->name('debug.design');

        // Dev User Switcher — log in as any user without a password
        Route::get('/switch-user', function () {
            $usersByRole = \App\Models\User::orderBy('name')
                ->get()
                ->groupBy('role');

            return view('debug.switch-user', compact('usersByRole'));
        })->name('debug.switch-user');

        Route::get('/switch-user/{user}', function (\App\Models\User $user) {
            \Illuminate\Support\Facades\Auth::login($user);

            $redirect = match ($user->role) {
                'company_administrator' => route('admin.dashboard'),
                default => route('dashboard'),
            };

            return redirect($redirect)->with('status', "Logged in as {$user->name} ({$user->role})");
        })->name('debug.switch-user.post');
    });
}

/*
|--------------------------------------------------------------------------
| Blog Routes (Before CMS catch-all)
|--------------------------------------------------------------------------
*/

if (config('business.features.blog')) {
    // Public blog routes
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/search', [BlogSearchController::class, 'index'])->name('blog.search');
    Route::get('/blog/feed', [BlogController::class, 'feed'])->name('blog.feed');
    Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

    // Admin blog routes
    Route::middleware(['auth', 'companyAdministrator'])->prefix('admin')->group(function () {
        Route::resource('blog/posts', BlogPostController::class)->names([
            'index' => 'admin.blog.posts.index',
            'create' => 'admin.blog.posts.create',
            'store' => 'admin.blog.posts.store',
            'edit' => 'admin.blog.posts.edit',
            'update' => 'admin.blog.posts.update',
            'destroy' => 'admin.blog.posts.destroy',
        ]);

        Route::resource('blog/categories', BlogCategoryController::class)->names([
            'index' => 'admin.blog.categories.index',
            'create' => 'admin.blog.categories.create',
            'store' => 'admin.blog.categories.store',
            'edit' => 'admin.blog.categories.edit',
            'update' => 'admin.blog.categories.update',
            'destroy' => 'admin.blog.categories.destroy',
        ]);

        Route::resource('blog/tags', BlogTagController::class)->names([
            'index' => 'admin.blog.tags.index',
            'create' => 'admin.blog.tags.create',
            'store' => 'admin.blog.tags.store',
            'edit' => 'admin.blog.tags.edit',
            'update' => 'admin.blog.tags.update',
            'destroy' => 'admin.blog.tags.destroy',
        ]);

        Route::get('/blog/import', [WordPressImportController::class, 'index'])->name('admin.blog.import.index');
        Route::post('/blog/import', [WordPressImportController::class, 'import'])->name('admin.blog.import.process');

        // AI Content Generation Routes
        Route::prefix('ai')->group(function () {
            Route::post('/generate', [\App\Http\Controllers\Admin\AIContentController::class, 'generate'])->name('admin.ai.generate');
            Route::post('/seo', [\App\Http\Controllers\Admin\AIContentController::class, 'generateSEO'])->name('admin.ai.seo');
            Route::get('/stats', [\App\Http\Controllers\Admin\AIContentController::class, 'getUsageStats'])->name('admin.ai.stats');
            Route::get('/settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'index'])->name('admin.ai.settings.index');
            Route::patch('/settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'update'])->name('admin.ai.settings.update');
            Route::post('/fetch-models', [\App\Http\Controllers\Admin\AISettingsController::class, 'fetchModels'])->name('admin.ai.fetch-models');
        });
    });
}

/*
|--------------------------------------------------------------------------
| CMS Catch-All Routes (MUST BE LAST!)
|--------------------------------------------------------------------------
| These catch-all routes MUST be defined LAST so they don't override
| any specific routes defined above (auth, admin, API, etc.)
*/

if (config('business.features.cms')) {
    // Form submission handler for CMS pages with forms
    Route::post('/cms/form/{slug}', [CmsFormSubmissionController::class, 'submit'])
        ->middleware('throttle:15,1')
        ->name('cms.form.submit')
        ->where('slug', '[a-z0-9\-]+');

    // Extreme — Laravel full-stack app generator landing page
    Route::get('/extreme', function () {
        return view('extreme');
    })->name('extreme');

    // Extreme — interactive smoke-and-mirrors demo
    Route::get('/extreme/demo', function () {
        return view('extreme-demo');
    })->name('extreme.demo');

    // Extreme intake form submission
    Route::post('/extreme/intake', [App\Http\Controllers\ContactController::class, 'submitCleanSlateIntake'])->name('extreme.intake');

    // Extreme — pricing / plans page
    Route::get('/extreme/plans', [\App\Modules\CleanSlate\Http\Controllers\BillingController::class, 'plans'])->name('cleanslate.billing.plans');

    // CMS page display - CATCH-ALL route (matches any remaining /{slug})
    // Since this is last, all specific routes above will match first
    Route::get('/{slug}', [CmsPagePublicController::class, 'show'])
        ->name('cms.show')
        ->where('slug', '[a-z0-9\-]+');
}
