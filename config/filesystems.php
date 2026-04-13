<?php

// If running on Laravel Cloud, the environment may provide a JSON payload
// in LARAVEL_CLOUD_DISK_CONFIG describing one or more S3-compatible disks.
// We'll parse it and, when present, map the `private` (and optional `public`)
// disks to S3 automatically. Falls back to local/AWS_* if not provided.

$cloudRaw = env('LARAVEL_CLOUD_DISK_CONFIG');
$laravelCloudPrivate = null;
$laravelCloudPublic = null;

if ($cloudRaw) {
    $list = json_decode($cloudRaw, true);
    if (is_array($list)) {
        foreach ($list as $item) {
            // Validate that the cloud config has all required fields before using it
            $hasRequiredFields = ! empty($item['bucket']) &&
                ! empty($item['access_key_id']) &&
                ! empty($item['access_key_secret']);

            if (! $hasRequiredFields) {
                continue;
            }

            if (($item['disk'] ?? null) === 'private') {
                $laravelCloudPrivate = $item;
            }
            if (($item['disk'] ?? null) === 'public') {
                $laravelCloudPublic = $item;
            }
            // If a default disk is flagged and no explicit private provided, use it
            if (($item['is_default'] ?? false) && ! $laravelCloudPrivate) {
                $laravelCloudPrivate = $item;
            }
        }
    }
}

// Check if cloud storage bucket is connected (AWS S3 or S3-compatible)
// Only enable cloud storage if ALL required credentials are present and not empty
// Note: We don't check FILESYSTEM_DRIVER here because we want to auto-detect based on credentials alone
$hasCloudStorage = ($laravelCloudPrivate !== null) || (
    ! empty(env('AWS_ACCESS_KEY_ID')) &&
    ! empty(env('AWS_SECRET_ACCESS_KEY')) &&
    ! empty(env('AWS_BUCKET'))
);

// For public cloud, we need the same check (can be the same bucket or separate)
$hasPublicCloudStorage = ($laravelCloudPublic !== null) || (
    ! empty(env('AWS_ACCESS_KEY_ID')) &&
    ! empty(env('AWS_SECRET_ACCESS_KEY')) &&
    ! empty(env('AWS_BUCKET'))
);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // Cloud-ready disks for production (use S3 or S3-compatible storage)
        // If cloud storage is connected, use it; otherwise fall back to local storage
        'private' => $laravelCloudPrivate ? [
            'driver' => 's3',
            'key' => $laravelCloudPrivate['access_key_id'] ?? null,
            'secret' => $laravelCloudPrivate['access_key_secret'] ?? null,
            'region' => $laravelCloudPrivate['default_region'] ?? 'us-west-2',
            'bucket' => $laravelCloudPrivate['bucket'] ?? null,
            'url' => $laravelCloudPrivate['url'] ?? null,
            'endpoint' => $laravelCloudPrivate['endpoint'] ?? null,
            'use_path_style_endpoint' => $laravelCloudPrivate['use_path_style_endpoint'] ?? false,
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ] : ($hasCloudStorage ? [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ]),

        'public_cloud' => $laravelCloudPublic ? [
            'driver' => 's3',
            'key' => $laravelCloudPublic['access_key_id'] ?? null,
            'secret' => $laravelCloudPublic['access_key_secret'] ?? null,
            'region' => $laravelCloudPublic['default_region'] ?? 'us-east-1',
            'bucket' => $laravelCloudPublic['bucket'] ?? null,
            'url' => $laravelCloudPublic['url'] ?? null,
            'endpoint' => $laravelCloudPublic['endpoint'] ?? null,
            'use_path_style_endpoint' => $laravelCloudPublic['use_path_style_endpoint'] ?? false,
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ] : ($hasPublicCloudStorage ? [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ] : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ]),

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
