<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
