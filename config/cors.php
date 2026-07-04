<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Mengizinkan ALL paths (termasuk api/*, web, storage, dll.)
    'paths' => ['*'],

    // Mengizinkan ALL HTTP Methods (GET, POST, PUT, DELETE, OPTIONS, dll.)
    'allowed_methods' => ['*'],

    // Mengizinkan ALL Origins (Semua domain/localhost port berapa pun bebas akses)
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    // Mengizinkan ALL Headers (Authorization, Content-Type, X-Requested-With, dll.)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];