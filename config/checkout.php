<?php

return [
    'psgc_base_url' => env('PSGC_API_URL', 'https://psgc.gitlab.io/api'),

    'payment_methods' => [
        'ANY' => 'All Xendit Channels',
        'GCASH' => 'GCash',
        'PAYMAYA' => 'PayMaya',
        'CARD' => 'Credit / Debit Card',
        'OTC' => 'Over the Counter',
    ],

    'payment_method_map' => [
        'ANY' => [],
        'GCASH' => ['GCASH'],
        'PAYMAYA' => ['PAYMAYA'],
        'CARD' => ['CARDS'],
        'OTC' => ['7ELEVEN', 'CEBUANA', 'MLHUILLIER', 'ECPAY'],
    ],
];
