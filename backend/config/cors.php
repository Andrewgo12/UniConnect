<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your CORS settings for your application.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:3001',
        'http://192.168.81.98:3000',
        'http://192.168.81.98:3001',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization', 'Content-Type', 'X-Requested-With'],

    'supports_credentials' => true,

    'max_age' => 0,

];