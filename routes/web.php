<?php

require __DIR__.'/web/public.php';
require __DIR__.'/auth.php';
require __DIR__.'/web/super_admin.php';
require __DIR__.'/web/debug.php';
require __DIR__.'/web/trial.php';

\Illuminate\Support\Facades\Route::middleware(['tenant', 'tenantOnly', 'tenantUser'])->group(function () {
    require __DIR__.'/web/client.php';
    require __DIR__.'/web/messages.php';
    require __DIR__.'/web/admin.php';
    require __DIR__.'/web/blog.php';
});
