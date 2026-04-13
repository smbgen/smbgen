<?php

namespace App\Modules\SaasProductModule;

use Illuminate\Support\ServiceProvider;

class SaasProductModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/saasproductmodule.php', 'saasproductmodule'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Resources/Views', 'saasproductmodule');
    }
}
