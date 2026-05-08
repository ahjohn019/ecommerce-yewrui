<?php

return [
    'rate_limits' => [
        'auth' => [
            'per_minute' => (int) env('API_RATE_LIMIT_AUTH_PER_MINUTE', 10),
        ],
        'products' => [
            'per_minute' => (int) env('API_RATE_LIMIT_PRODUCTS_PER_MINUTE', 60),
        ],
    ],
];
