<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blog Comments
    |--------------------------------------------------------------------------
    */

    'comments_enabled' => env('BLOG_COMMENTS_ENABLED', true),
    'auto_approve_comments' => env('BLOG_AUTO_APPROVE_COMMENTS', false),
    'guest_comments_enabled' => env('BLOG_GUEST_COMMENTS', true),
    'comment_max_length' => 2000,
    'comments_per_page' => 50,

    /*
    |--------------------------------------------------------------------------
    | Blog Search & Display
    |--------------------------------------------------------------------------
    */

    'posts_per_page' => 12,
    'search_results_per_page' => 15,
    'related_posts_count' => 3,

    /*
    |--------------------------------------------------------------------------
    | RSS Feed
    |--------------------------------------------------------------------------
    */

    'rss_enabled' => true,
    'rss_posts_limit' => 50,

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'sitemap_enabled' => true,
    'structured_data_enabled' => true,
    'auto_generate_seo' => true,

    /*
    |--------------------------------------------------------------------------
    | Content Block Types
    |--------------------------------------------------------------------------
    */

    'available_block_types' => [
        'heading',
        'text',
        'image',
        'quote',
        'code',
        'video',
        'callout',
        'gallery',
        'accordion',
        'columns',
        'embed',
        'button',
        'divider',
        'table',
    ],
];
