<?php

    return [

        'mailgun' => [
            'domain' => env('MAILGUN_DOMAIN'),
            'secret' => env('MAILGUN_SECRET'),
            'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
            'scheme' => 'https',
        ],
        'cookie' => env('SESSION_COOKIE', 'yagoda_session'),
        'domain' => env('SESSION_DOMAIN', '.yagoda.team'),
        'secure' => env('SESSION_SECURE_COOKIE', true),
        'same_site' => 'lax',


    ];
