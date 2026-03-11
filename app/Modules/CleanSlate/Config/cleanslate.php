<?php

return [
    'stripe' => [
        'prices' => [
            'basic'        => env('CLEANSLATE_STRIPE_PRICE_BASIC'),
            'professional' => env('CLEANSLATE_STRIPE_PRICE_PROFESSIONAL'),
            'executive'    => env('CLEANSLATE_STRIPE_PRICE_EXECUTIVE'),
        ],
    ],

    'tiers' => [
        'basic'        => 1,
        'professional' => 2,
        'executive'    => 3,
    ],
];
