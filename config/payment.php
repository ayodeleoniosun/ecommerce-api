<?php

return [
    'gateways' => [
        'korapay' => [
            'base_url' => env('KORA_BASE_URL'),
            'public_key' => env('KORA_PUBLIC_KEY'),
            'secret_key' => env('KORA_SECRET_KEY'),
            'encryption_key' => env('KORA_ENCRYPTION_KEY'),
        ],
        'flutterwave' => [
            'base_url' => env('FLUTTERWAVE_BASE_URL'),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
        ],
    ],
];
