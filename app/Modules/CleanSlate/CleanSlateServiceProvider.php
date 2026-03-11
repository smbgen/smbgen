<?php

namespace App\Modules\CleanSlate;

use Illuminate\Support\ServiceProvider;

class CleanSlateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/cleanslate.php', 'cleanslate'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Resources/Views', 'cleanslate');
    }
}
