<?php

use App\Http\Controllers\CmsPagePublicController;
use Illuminate\Support\Facades\Route;

Route::get('/{slug}', [CmsPagePublicController::class, 'show'])
    ->name('cms.show')
    ->where('slug', '[a-z0-9\-]+');
