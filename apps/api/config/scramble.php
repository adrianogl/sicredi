<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'description' => 'REST API for managing motions and voting sessions (Sicredi technical test).',
    ],

    'ui' => [
        'title' => 'Sicredi API',
        'theme' => 'light',
        'layout' => 'responsive',
    ],

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],
];
