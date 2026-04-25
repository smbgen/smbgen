<?php

use App\Models\CmsPage;
use App\Models\User;
use App\Support\ModuleRegistry;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (ModuleRegistry::isEnabled('frontend_site') && ModuleRegistry::isSelectedFrontend('frontend_site')) {
        return view('frontend.home-platform');
    }

    if (! auth()->check()) {
        if (config('business.features.cms')) {
            $homePage = CmsPage::query()
                ->where('slug', 'home')
                ->where('is_published', true)
                ->first();

            if ($homePage) {
                return view('cms.show', ['page' => $homePage]);
            }
        }

        return redirect()->route('login');
    }

    $authenticatedUser = auth()->user();

    if ($authenticatedUser->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    }

    if (in_array($authenticatedUser->role, [User::ROLE_ADMINISTRATOR, User::ROLE_ADMINISTRATOR_LEGACY, 'company_administrator'], true)) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
})->name('home');

Route::middleware('moduleEnabled:frontend_site')->group(function () {
    Route::get('/platform', fn () => view('frontend.home-platform'))->name('home.platform');
    Route::get('/services', fn () => view('frontend.home-services'))->name('home.services');
    if (app()->isLocal()) {
        Route::get('/overview-deck', fn () => view('frontend.overview-deck'))->name('overview.deck');
    }
    Route::get('/features', fn () => view('frontend.features'))->name('features');
    Route::get('/solutions', fn () => view('frontend.solutions'))->name('solutions');
    Route::get('/google-workspace', fn () => view('frontend.google-workspace'))->name('google.workspace');

    Route::prefix('industries')->name('industries.')->group(function () {
        Route::get('/', fn () => view('frontend.industries.index'))->name('index');
        Route::get('/real-estate', fn () => view('frontend.industries.real-estate'))->name('real-estate');
        Route::get('/home-services', fn () => view('frontend.industries.home-services'))->name('home-services');
        Route::get('/legal', fn () => view('frontend.industries.legal'))->name('legal');
        Route::get('/health-wellness', fn () => view('frontend.industries.health-wellness'))->name('health-wellness');
        Route::get('/consulting', fn () => view('frontend.industries.consulting'))->name('consulting');
    });

    Route::get('/solutions/contact', fn () => view('frontend.product-page', ['productId' => 'contact-core']))->name('product.contact');
    Route::get('/solutions/book', fn () => view('frontend.product-page', ['productId' => 'book-core']))->name('product.book');
    Route::get('/solutions/pay', fn () => view('frontend.product-page', ['productId' => 'pay-core']))->name('product.pay');
    Route::get('/solutions/portal', fn () => view('frontend.product-page', ['productId' => 'portal-core']))->name('product.portal');
    Route::get('/solutions/crm', fn () => view('frontend.product-page', ['productId' => 'crm-core']))->name('product.crm');
    Route::get('/solutions/cms', fn () => view('frontend.product-page', ['productId' => 'cms-core']))->name('product.cms');

    Route::prefix('solutions')->name('solutions.')->group(function () {
        Route::get('/areas', fn () => view('frontend.solutions.index'))->name('areas');
        Route::get('/more-leads', fn () => view('frontend.solutions.more-leads'))->name('more-leads');
        Route::get('/streamline-bookings', fn () => view('frontend.solutions.streamline-bookings'))->name('streamline-bookings');
        Route::get('/get-paid-faster', fn () => view('frontend.solutions.get-paid-faster'))->name('get-paid-faster');
        Route::get('/retain-clients', fn () => view('frontend.solutions.retain-clients'))->name('retain-clients');
        Route::get('/grow-referrals', fn () => view('frontend.solutions.grow-referrals'))->name('grow-referrals');
    });

});

// CMS public routes are registered in routes/web/content.php without the
// moduleEnabled:frontend_site gate, so they work independently of this module.
// Contact and booking public routes are registered in routes/web/public.php.
