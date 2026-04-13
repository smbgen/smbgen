<?php

use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\WordPressImportController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSearchController;
use Illuminate\Support\Facades\Route;

if (config('business.features.blog')) {
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/search', [BlogSearchController::class, 'index'])->name('blog.search');
    Route::get('/blog/feed', [BlogController::class, 'feed'])->name('blog.feed');
    Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

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
