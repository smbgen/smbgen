<?php

// Pre-autoload directory creation script
// This runs before Composer generates the autoload files

$directories = [
    'storage',
    'storage/framework',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($directories as $dir) {
    if (! is_dir($dir)) {
        if (! mkdir($dir, 0755, true)) {
            // Silently continue - directories might be created by other means
            continue;
        }
    }
}

// Always exit successfully to not break composer install
exit(0);
