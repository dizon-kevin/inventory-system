<?php

return [
    'psgc_base_url' => env('PSGC_API_URL', 'https://psgc.gitlab.io/api'),

    'payment_methods' => [
        'XENDIT' => 'Xendit',
    ],

    'payment_method_map' => [
        'XENDIT' => [],
    ],
];
