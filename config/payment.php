<?php

return [
    'default_gateway' => env('PAYMENT_GATEWAY', 'midtrans'),
    
    'gateways' => [
        'midtrans' => [
            'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-YOUR_SERVER_KEY'),
            'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-YOUR_CLIENT_KEY'),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
            'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
            'is_3ds' => env('MIDTRANS_IS_3DS', true),
        ],
        
        'xendit' => [
            'secret_key' => env('XENDIT_SECRET_KEY', 'xnd_development_YOUR_SECRET_KEY'),
            'public_key' => env('XENDIT_PUBLIC_KEY', 'xnd_public_development_YOUR_PUBLIC_KEY'),
            'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', 'YOUR_WEBHOOK_TOKEN'),
            'is_production' => env('XENDIT_IS_PRODUCTION', false),
        ],
        
        'manual' => [
            'enabled' => env('MANUAL_PAYMENT_ENABLED', true),
            'bank_accounts' => [
                [
                    'bank' => 'BCA',
                    'account_number' => '1234567890',
                    'account_name' => 'SMK Bakti Nusantara 666',
                ],
                [
                    'bank' => 'Mandiri',
                    'account_number' => '0987654321',
                    'account_name' => 'SMK Bakti Nusantara 666',
                ],
                [
                    'bank' => 'BNI',
                    'account_number' => '5555666677',
                    'account_name' => 'SMK Bakti Nusantara 666',
                ]
            ]
        ]
    ],
    
    'payment_methods' => [
        'virtual_account' => [
            'bca' => true,
            'bni' => true,
            'bri' => true,
            'mandiri' => true,
            'permata' => true,
        ],
        'e_wallet' => [
            'gopay' => true,
            'ovo' => true,
            'dana' => true,
            'linkaja' => true,
        ],
        'qris' => true,
        'credit_card' => true,
        'bank_transfer' => true,
        'manual_transfer' => true,
    ],
    
    'transaction' => [
        'expiry_duration' => env('PAYMENT_EXPIRY_DURATION', 24), // hours
        'currency' => 'IDR',
        'registration_fee' => 250000,
    ],
    
    'webhook' => [
        'midtrans_url' => '/webhook/midtrans',
        'xendit_url' => '/webhook/xendit',
    ],
    
    'security' => [
        'encrypt_sensitive_data' => true,
        'log_all_transactions' => true,
        'fraud_detection' => true,
    ]
];