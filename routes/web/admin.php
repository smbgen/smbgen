<?php

use App\Http\Controllers\Admin\AdminBillingController;
use App\Http\Controllers\Admin\AdminClientFileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DomainOnboardingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeadFormController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'companyAdministrator'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/stop-impersonating', [SuperAdminTenantController::class, 'stopImpersonating'])->name('admin.stop-impersonating');

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin');

    Route::get('/test', function () {
        return view('admin.test');
    })->name('admin.test');

    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'search'])->name('admin.search');
    Route::get('/search/stats', [\App\Http\Controllers\Admin\SearchController::class, 'stats'])->name('admin.search.stats');

    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('admin.leads.index');
    Route::get('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('admin.leads.show');
    Route::post('/leads/{lead}/convert', [\App\Http\Controllers\Admin\LeadController::class, 'convertToClient'])->name('admin.leads.convert');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('admin.leads.destroy');
    Route::get('/leads/export/csv', [\App\Http\Controllers\Admin\LeadController::class, 'exportCsv'])->name('admin.leads.export.csv');
    Route::post('/leads/toggle-notifications', [\App\Http\Controllers\Admin\LeadController::class, 'toggleNotifications'])->name('admin.leads.toggle-notifications');

    if (config('business.features.inspection_reports', false)) {
        Route::post('/inspection-reports', [\App\Http\Controllers\Admin\InspectionReportController::class, 'store'])->name('admin.inspection-reports.store');
        Route::get('/inspection-reports', [\App\Http\Controllers\Admin\InspectionReportController::class, 'index'])->name('admin.inspection-reports.index');
        Route::get('/inspection-reports/create', [\App\Http\Controllers\Admin\InspectionReportController::class, 'create'])->name('admin.inspection-reports.create');
        Route::get('/inspection-reports/{report}', [\App\Http\Controllers\Admin\InspectionReportController::class, 'show'])->name('admin.inspection-reports.show');
        Route::post('/inspection-reports/{report}/resend', [\App\Http\Controllers\Admin\InspectionReportController::class, 'resend'])->name('admin.inspection-reports.resend');
        Route::post('/inspection-reports/{report}/store-to-drive', [\App\Http\Controllers\Admin\InspectionReportController::class, 'storeToGoogleDrive'])->name('admin.inspection-reports.store-to-drive');
    }

    Route::post('/leads/{lead}/convert-legacy', [LeadFormController::class, 'convert'])->name('leads.convert');
    Route::get('/leads/partial', [LeadFormController::class, 'partial'])->name('leads.partial');

    Route::get('/clients/export/csv', [ClientController::class, 'exportCsv'])->name('clients.export.csv');

    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('admin.users.updatePassword');

    Route::get('/game', function () {
        return view('admin.game');
    })->name('admin.game');

    Route::get('/clients/files', [AdminClientFileController::class, 'all'])->name('admin.clients.files.overview');
    Route::get('/clients/{client}/files', [AdminClientFileController::class, 'index'])->name('admin.client.files');
    Route::post('/clients/{client}/files', [AdminClientFileController::class, 'store'])->name('admin.client.files.upload');
    Route::delete('/clients/{client}/files/{file}', [AdminClientFileController::class, 'destroy'])->name('admin.client.files.destroy');
    Route::get('/clients/{client}/files/{file}/download', [AdminClientFileController::class, 'download'])->name('admin.client.files.download');

    Route::get('/users/{user}/files', [AdminClientFileController::class, 'userFiles'])->name('admin.user.files');
    Route::post('/users/{user}/files', [AdminClientFileController::class, 'storeUserFile'])->name('admin.user.files.upload');
    Route::delete('/users/{user}/files/{file}', [AdminClientFileController::class, 'destroyUserFile'])->name('admin.user.files.destroy');
    Route::get('/users/{user}/files/{file}/download', [AdminClientFileController::class, 'downloadUserFile'])->name('admin.user.files.download');

    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/provision', [ClientController::class, 'provision'])->name('clients.provision');
    Route::post('/clients/{client}/link-google', [ClientController::class, 'linkGoogleId'])->name('clients.link-google');
    Route::patch('/clients/{client}/toggle-access', [ClientController::class, 'toggleAccess'])->name('clients.toggle-access');

    Route::get('/clients-import', [\App\Http\Controllers\ClientImportController::class, 'index'])->name('clients.import.index');
    Route::post('/clients-import/upload', [\App\Http\Controllers\ClientImportController::class, 'upload'])->name('clients.import.upload');
    Route::get('/clients-import/{clientImport}/preview', [\App\Http\Controllers\ClientImportController::class, 'preview'])->name('clients.import.preview');
    Route::post('/clients-import/{clientImport}/process', [\App\Http\Controllers\ClientImportController::class, 'process'])->name('clients.import.process');
    Route::get('/clients-import/history', [\App\Http\Controllers\ClientImportController::class, 'history'])->name('clients.import.history');

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

    Route::get('/settings', [\App\Http\Controllers\Admin\BusinessSettingsController::class, 'index'])->name('admin.business_settings.index');
    Route::patch('/settings', [\App\Http\Controllers\Admin\BusinessSettingsController::class, 'update'])->name('admin.business_settings.update');

    Route::get('/setup-wizard', [\App\Http\Controllers\Admin\SetupWizardController::class, 'index'])->name('admin.setup-wizard.index');
    Route::get('/setup-wizard/{step}', [\App\Http\Controllers\Admin\SetupWizardController::class, 'show'])->name('admin.setup-wizard.show');
    Route::post('/setup-wizard/business', [\App\Http\Controllers\Admin\SetupWizardController::class, 'saveBusiness'])->name('admin.setup-wizard.business');
    Route::post('/setup-wizard/theme', [\App\Http\Controllers\Admin\SetupWizardController::class, 'saveTheme'])->name('admin.setup-wizard.theme');
    Route::post('/setup-wizard/first-page', [\App\Http\Controllers\Admin\SetupWizardController::class, 'generateFirstPage'])->name('admin.setup-wizard.first-page');
    Route::post('/setup-wizard/skip', [\App\Http\Controllers\Admin\SetupWizardController::class, 'skipStep'])->name('admin.setup-wizard.skip');
    Route::post('/setup-wizard/complete', [\App\Http\Controllers\Admin\SetupWizardController::class, 'complete'])->name('admin.setup-wizard.complete');
    Route::post('/setup-wizard/dismiss', [\App\Http\Controllers\Admin\SetupWizardController::class, 'dismiss'])->name('admin.setup-wizard.dismiss');

    Route::get('/environment-settings', [\App\Http\Controllers\Admin\EnvironmentSettingsController::class, 'index'])->name('admin.environment_settings.index');
    Route::patch('/environment-settings', [\App\Http\Controllers\Admin\EnvironmentSettingsController::class, 'update'])->name('admin.environment_settings.update');

    Route::get('/onboarding', [\App\Http\Controllers\Admin\BusinessSettingsController::class, 'onboarding'])->name('admin.onboarding');
    Route::get('/domain-onboarding', [DomainOnboardingController::class, 'show'])->name('admin.domain-onboarding.show');
    Route::patch('/domain-onboarding', [DomainOnboardingController::class, 'update'])->name('admin.domain-onboarding.update');
    Route::get('/google-oauth', [AdminDashboardController::class, 'googleOAuth'])->name('admin.google-oauth');

    if (config('app.debug')) {
        Route::post('/email/test', [AdminDashboardController::class, 'sendTestEmail'])->name('admin.email.test');
    }

    Route::get('/email', [\App\Http\Controllers\Admin\EmailController::class, 'index'])->name('admin.email.index');
    Route::post('/email/send', [\App\Http\Controllers\Admin\EmailController::class, 'send'])->name('admin.email.send');
    Route::get('/email/template', [\App\Http\Controllers\Admin\EmailController::class, 'getTemplate'])->name('admin.email.template');
    Route::get('/email/booking-emails', [\App\Http\Controllers\Admin\EmailController::class, 'getBookingEmails'])->name('admin.email.booking-emails');
    Route::get('/email/all-emails', [\App\Http\Controllers\Admin\EmailController::class, 'getAllEmails'])->name('admin.email.all-emails');

    Route::resource('availability', \App\Http\Controllers\Admin\AvailabilityController::class)->names([
        'index' => 'admin.availability.index',
        'create' => 'admin.availability.create',
        'store' => 'admin.availability.store',
        'edit' => 'admin.availability.edit',
        'update' => 'admin.availability.update',
        'destroy' => 'admin.availability.destroy',
    ]);

    Route::post('/availability/blackout', [\App\Http\Controllers\Admin\AvailabilityController::class, 'storeBlackout'])->name('admin.availability.blackout.store');
    Route::delete('/availability/blackout/{blackoutDate}', [\App\Http\Controllers\Admin\AvailabilityController::class, 'destroyBlackout'])->name('admin.availability.blackout.destroy');

    if (config('business.features.cms')) {
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

    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/bookings/dashboard', [\App\Http\Controllers\Admin\BookingController::class, 'dashboard'])->name('admin.bookings.dashboard');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('/bookings/{booking}/convert-to-client', [\App\Http\Controllers\Admin\BookingController::class, 'convertToClient'])->name('admin.bookings.convert-to-client');
    Route::post('/bookings/{booking}/send-reminder', [\App\Http\Controllers\Admin\BookingController::class, 'sendReminder'])->name('admin.bookings.send-reminder');
    Route::delete('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('admin.bookings.destroy');

    Route::get('/booking-fields', [\App\Http\Controllers\Admin\BookingFieldConfigController::class, 'edit'])->name('admin.booking-fields.edit');
    Route::put('/booking-fields', [\App\Http\Controllers\Admin\BookingFieldConfigController::class, 'update'])->name('admin.booking-fields.update');

    Route::get('/email-logs', [\App\Http\Controllers\Admin\EmailLogController::class, 'index'])->name('admin.email-logs.index');
    Route::get('/email-logs/{emailLog}', [\App\Http\Controllers\Admin\EmailLogController::class, 'show'])->name('admin.email-logs.show');
    Route::post('/email-logs/{emailLog}/resend', [\App\Http\Controllers\Admin\EmailLogController::class, 'resend'])->name('admin.email-logs.resend');
    Route::delete('/email-logs/{emailLog}', [\App\Http\Controllers\Admin\EmailLogController::class, 'destroy'])->name('admin.email-logs.destroy');
    Route::post('/email-logs/test-smtp', [\App\Http\Controllers\Admin\EmailLogController::class, 'testSmtp'])->name('admin.email-logs.test-smtp');

    Route::get('/docs', [\App\Http\Controllers\Admin\AdminDocsController::class, 'index'])->name('admin.docs.index');

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

    Route::post('/users/{user}/magic-link/send', [\App\Http\Controllers\MagicLinkController::class, 'send'])->name('admin.users.magiclink.send');

    Route::get('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('admin.calendar.index');
    Route::get('/calendar/redirect', [\App\Http\Controllers\Admin\CalendarController::class, 'redirectToGoogle'])->name('admin.calendar.redirect');
    Route::get('/calendar/connect', [\App\Http\Controllers\Admin\CalendarController::class, 'redirectToGoogle'])->name('admin.calendar.connect');
    Route::get('/calendar/callback', [\App\Http\Controllers\Admin\CalendarController::class, 'handleGoogleCallback'])->name('admin.calendar.callback');
    Route::get('/calendar/select', [\App\Http\Controllers\Admin\CalendarController::class, 'selectCalendar'])->name('admin.calendar.select');
    Route::post('/calendar/update', [\App\Http\Controllers\Admin\CalendarController::class, 'updateCalendar'])->name('admin.calendar.update');
    Route::post('/calendar/disconnect', [\App\Http\Controllers\Admin\CalendarController::class, 'disconnect'])->name('admin.calendar.disconnect');

    Route::post('/users/{user}/elevate', [UserController::class, 'elevate'])->name('admin.users.elevate');
    Route::post('/users/{user}/verify', [UserController::class, 'verify'])->name('admin.users.verify');
    Route::post('/users/{user}/unverify', [UserController::class, 'unverify'])->name('admin.users.unverify');

    Route::get('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('admin.packages.index');
    Route::get('/packages/create', [\App\Http\Controllers\Admin\PackageController::class, 'create'])->name('admin.packages.create');
    Route::post('/packages/review', [\App\Http\Controllers\Admin\PackageController::class, 'review'])->name('admin.packages.review');
    Route::post('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'store'])->name('admin.packages.store');
    Route::get('/packages/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'show'])->name('admin.packages.show');
    Route::patch('/packages/{package}/status', [\App\Http\Controllers\Admin\PackageController::class, 'updateStatus'])->name('admin.packages.status');
    Route::patch('/packages/{package}/toggle-portal', [\App\Http\Controllers\Admin\PackageController::class, 'togglePortal'])->name('admin.packages.toggle-portal');
    Route::patch('/packages/{package}/files/{file}/promote', [\App\Http\Controllers\Admin\PackageController::class, 'togglePromote'])->name('admin.packages.files.promote');
    Route::get('/packages/{package}/files/{file}/preview', [\App\Http\Controllers\Admin\PackageController::class, 'previewFile'])->name('admin.packages.files.preview');
    Route::get('/packages/{package}/files/{file}/content', [\App\Http\Controllers\Admin\PackageController::class, 'fileContent'])->name('admin.packages.files.content');
});
