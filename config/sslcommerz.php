<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSLCommerz Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may specify your SSLCommerz store ID and password.
    | You can find these credentials in your SSLCommerz merchant panel.
    |
    */

    'store_id' => env('SSLCOMMERZ_STORE_ID'),

    'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | Set this to true if you want to use the SSLCommerz sandbox environment.
    | Set it to false for production.
    |
    */

    'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
];
