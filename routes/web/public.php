<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\LeadFormController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/track/email/{id}', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('/track/click/{id}', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');

Route::get('/assets/{path}', function (string $path) {
    if (! str_starts_with($path, 'user_files/') && ! str_starts_with($path, 'client_files/') && ! str_starts_with($path, 'cms/images/')) {
        abort(404);
    }

    $disk = Storage::disk('public_cloud');
    $diskConfig = config('filesystems.disks.public_cloud');

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

    try {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }
    } catch (\Throwable $e) {
    }

    abort(404);
})->where('path', '.*')->name('assets.public');

Route::get('/storage/{path}', function (string $path) {
    return redirect(url('/assets/'.ltrim($path, '/')), 301);
})->where('path', '.*')->name('storage.public');

Route::view('/eula', 'legal.eula')->name('legal.eula');
Route::view('/privacy', 'legal.privacy')->name('legal.privacy');

Route::get('/auth/google/redirect', [AuthenticatedSessionController::class, 'redirectToGoogle'])
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

Route::post('/leadform', [LeadFormController::class, 'store'])->name('leadform.store');

Route::get('/contact', function () {
    if (config('business.features.cms')) {
        $contactPage = \App\Models\CmsPage::where('slug', 'contact')
            ->where('is_published', true)
            ->first();

        if ($contactPage) {
            return view('cms.show', ['page' => $contactPage]);
        }
    }

    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

if (config('business.features.booking')) {
    Route::get('/book', [BookingController::class, 'showWizard'])->name('booking.wizard');
    Route::get('/book/availability', [BookingController::class, 'availability'])->name('booking.availability');
    Route::post('/book', [BookingController::class, 'book'])->name('booking.book');
    Route::get('/book/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
}

Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
Route::get('/pay', [PaymentController::class, 'collect'])->name('payment.collect');
Route::post('/pay/process', [PaymentController::class, 'process'])->name('payment.process');
Route::post('/pay/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');

Route::get('/magic-link/{token}/consume', [\App\Http\Controllers\MagicLinkController::class, 'consume'])
    ->middleware('throttle:10,1')
    ->name('magic.consume');

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
