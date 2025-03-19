<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'vbrr' => [
        'username' => env('PAYMENT_USERNAME', 'RateWell-api'),
        'password' => env('PAYMENT_PASSWORD', 'RateWell'),
        'return_url' => env('PAYMENT_RETURN_URL', 'https://mybestmerchantreturnurl.com'),
        'fail_url' => env('PAYMENT_FAIL_URL', 'https://mybestmerchantfailurl.com'),
    ],

    'yclients' => [
        'base_url' => env('YCLIENTS_BASE_URL'),
        'login' => env('YCLIENTS_LOGIN'),
        'password' => env('YCLIENTS_PASSWORD'),
    ],
];
