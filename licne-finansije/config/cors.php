<?php

    return [

        'paths' => ['api/*'],   // dozvoljava CORS samo za API rute

        'allowed_methods' => ['*'],   // sve HTTP metode (GET, POST, PUT, DELETE...)

        'allowed_origins' => [
            'http://localhost:3000',  // React dev server
            'http://localhost',       // Nginx u produkciji
        ],

        'allowed_origins_patterns' => [],

        'allowed_headers' => ['*'],   // dozvoljava sve header-e

        'exposed_headers' => [],

        'max_age' => 0,

        'supports_credentials' => true,
    ];