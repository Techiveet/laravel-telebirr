<?php

return [
    // Your App ID from the developer portal
    'app_id' => env('TELEBIRR_APP_ID', ''),
    
    // The Short Code provided by Telebirr
    'short_code' => env('TELEBIRR_SHORT_CODE', ''),
    
    // The Merchant App ID from the developer portal
    'merchant_id' => env('TELEBIRR_MERCHANT_ID', ''),

    // Your private key, including the BEGIN and END headers
    'private_key' => env('TELEBIRR_PRIVATE_KEY', ''),

    // Telebirr's (Zongheng) public key, just the long string
    'public_key' => env('TELEBIRR_PUBLIC_KEY', ''),
    
    // The API endpoint from the documentation
    'api_url' => env('TELEBIRR_API_URL', 'http://196.188.120.3:38443/apiaccess/payment/gateway'),
];