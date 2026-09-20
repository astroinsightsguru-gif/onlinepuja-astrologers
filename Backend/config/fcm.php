<?php

return [
    /*
     * |--------------------------------------------------------------------------
     * | Firebase Project ID
     * |--------------------------------------------------------------------------
     */
    'project_id' => env('FIREBASE_PROJECT_ID', ''),

    /*
     * |--------------------------------------------------------------------------
     * | Firebase Service Account Credentials
     * |--------------------------------------------------------------------------
     * | These are used to generate the OAuth2 access token for FCM v1 API.
     * | Get these values from your Firebase Console > Project Settings >
     * | Service Accounts > Generate new private key (JSON file).
     */
    'service_account' => [
        'type' => 'service_account',
        'project_id' => env('FIREBASE_PROJECT_ID', ''),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID', ''),
        'private_key' => env('FIREBASE_PRIVATE_KEY', ''),
        'client_email' => env('FIREBASE_CLIENT_EMAIL', ''),
        'client_id' => env('FIREBASE_CLIENT_ID', ''),
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => env('FIREBASE_CLIENT_CERT_URL', ''),
        'universe_domain' => 'googleapis.com',
    ],
];
