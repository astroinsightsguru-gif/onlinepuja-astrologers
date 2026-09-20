<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cashfree Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the Cashfree payment gateway configuration for your
    | Online Puja application. Replace the placeholder values with your actual
    | Cashfree credentials obtained from the Cashfree dashboard.
    |
    | Cashfree is an Indian payment gateway that supports multiple payment
    | methods including credit/debit cards, net banking, wallets, and UPI.
    |
    | For production deployment, ensure these credentials are securely stored
    | in environment variables rather than being committed to version control.
    */

    'app_id' => env('CASHFREE_APP_ID', 'your_cashfree_app_id_here'),

    'secret_key' => env('CASHFREE_SECRET_KEY', 'your_cashfree_secret_key_here'),

    'base_url' => env('CASHFREE_BASE_URL', 'https://sandbox.cashfree.com/'),

    'webhook_secret' => env('CASHFREE_WEBHOOK_SECRET', 'your_webhook_secret_here'),

    'is_sandbox' => env('CASHFREE_IS_SANDBOX', true), // Set to false for production

    'minimum_amount' => env('CASHFREE_MINIMUM_AMOUNT', 1.00),

    'maximum_amount' => env('CASHFREE_MAXIMUM_AMOUNT', 100000.00),

    'currency' => env('CASHFREE_CURRENCY', 'INR'),

    'order_timeout' => env('CASHFREE_ORDER_TIMEOUT', 30), // in minutes

    'retry_attempts' => env('CASHFREE_RETRY_ATTEMPTS', 3),

    'payment_modes' => [
        'card',
        'netbanking',
        'wallet',
        'upi',
    ],

    'supported_languages' => [
        'en',
        'hi',
        'bn',
        'gu',
        'mr',
        'kn',
        'ml',
        'pa',
        'ta',
        'te',
        'ma',
        'or',
        'as',
    ],
];