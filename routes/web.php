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

// Module routes must load before the CMS catch-all slug route
$moduleRouteFiles = glob(app_path('Modules/*/Routes/web.php')) ?: [];
usort($moduleRouteFiles, function (string $left, string $right): int {
    $leftIsFrontend = str_contains($left, '/FrontendSite/');
    $rightIsFrontend = str_contains($right, '/FrontendSite/');
    if ($leftIsFrontend === $rightIsFrontend) {
        return strcmp($left, $right);
    }
    return $leftIsFrontend ? 1 : -1;
});
foreach ($moduleRouteFiles as $moduleRouteFile) {
    require $moduleRouteFile;
}
unset($moduleRouteFiles, $moduleRouteFile);

// CMS public routes (form submission + catch-all slug) must be last
// so they don't intercept specific named routes defined above.
require __DIR__.'/web/content.php';
