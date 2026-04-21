<?php

use App\Http\Controllers\CmsFormSubmissionController;
use App\Http\Controllers\CmsPagePublicController;
use Illuminate\Support\Facades\Route;

if (config('business.features.cms')) {
    Route::post('/cms/form/{slug}', [CmsFormSubmissionController::class, 'submit'])
        ->middleware('throttle:15,1')
        ->name('cms.form.submit')
        ->where('slug', '[a-z0-9\-]+');

    Route::get('/{slug}', [CmsPagePublicController::class, 'show'])
        ->name('cms.show')
        ->where('slug', '[a-z0-9\-]+');
}
