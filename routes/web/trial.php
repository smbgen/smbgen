<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'centralOnly'])->group(function () {
    Route::get('/trial', [App\Http\Controllers\TrialController::class, 'show'])->name('trial.show');
    Route::get('/signup', [App\Http\Controllers\TrialController::class, 'show'])->name('signup');
    Route::post('/trial', [App\Http\Controllers\TrialController::class, 'register'])->name('trial.register');
    Route::post('/signup', [App\Http\Controllers\TrialController::class, 'register'])->name('signup.register');
    Route::get('/trial/google/redirect', [App\Http\Controllers\TrialController::class, 'googleRedirect'])->name('trial.google.redirect');
});
