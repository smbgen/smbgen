<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EmailEventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listeners are auto-discovered in Laravel 12
        // LogSentEmail listener will be automatically registered
    }
}
