<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Marketplace Settings
    |--------------------------------------------------------------------------
    |
    | Central place for storefront level configuration so the look and feel
    | of AkzoneScripts can be tuned without touching the code.
    |
    */

    'name' => env('APP_NAME', 'AkzoneScripts'),

    'version' => '1.0.0',

    'tagline' => 'Premium scripts, code & design assets for modern builders.',

    'currency' => env('MARKETPLACE_CURRENCY', 'USD'),

    'currency_symbol' => env('MARKETPLACE_CURRENCY_SYMBOL', '$'),

    // Vendor / owner contact details surfaced in the footer and emails.
    'support_email' => env('MARKETPLACE_SUPPORT_EMAIL', 'support@akzonescripts.test'),

    'social' => [
        'twitter' => env('MARKETPLACE_TWITTER', '#'),
        'github' => env('MARKETPLACE_GITHUB', '#'),
        'discord' => env('MARKETPLACE_DISCORD', '#'),
    ],

    // Number of products shown per page in the catalog.
    'per_page' => 12,

    // Allowed file extensions for the downloadable product packages.
    'allowed_file_types' => ['zip', 'rar', 'tar', 'gz', '7z'],

    // Max upload size (in kilobytes) for product packages. Default 200 MB.
    'max_file_size' => 204800,
];
