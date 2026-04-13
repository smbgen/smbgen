<?php

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
