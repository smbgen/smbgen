<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load business settings from DB and merge with config
        try {
            $dbSettings = \App\Models\BusinessSetting::getAll();

            // Override config with DB settings
            foreach ($dbSettings as $key => $value) {
                config(["business.{$key}" => $value]);
            }
        } catch (\Exception $e) {
            // Ignore DB errors during migrations or when table doesn't exist yet
        }
    }
}
