<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telebirr API Credentials
    |--------------------------------------------------------------------------
    |
    | These are the credentials provided by Telebirr for the API.
    | It is recommended to load these from your .env file.
    |
    */
    'app_id' => env('TELEBIRR_APP_ID'),
    'app_key' => env('TELEBIRR_APP_KEY'),
    'short_code' => env('TELEBIRR_SHORT_CODE'),
    'public_key' => env('TELEBIRR_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    */
    'api_url' => env('TELEBIRR_API_URL', 'http://196.188.120.3:11443/ammapi/payment/service-openup/toTradeWebPay'),

    /*
    |--------------------------------------------------------------------------
    | Application URLs
    |--------------------------------------------------------------------------
    |
    | These URLs are used by Telebirr to redirect the user after payment
    | and to send notifications to your server. They should be full URLs.
    |
    */
    'return_url' => env('TELEBIRR_RETURN_URL', 'your-domain.com/telebirr/return'),
    'notify_url' => env('TELEBIRR_NOTIFY_URL', 'your-domain.com/telebirr/notify'),

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */
    'timeout_express' => env('TELEBIRR_TIMEOUT_EXPRESS', '30'), // In minutes
    'receive_name' => env('TELEBIRR_RECEIVE_NAME', 'Your Business Name'),
];