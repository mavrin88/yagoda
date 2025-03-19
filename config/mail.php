<?php

    return [

        'default' => env('MAIL_MAILER', 'log'),

        'mailers' => [
            'mailgun' => [
                'transport' => 'mailgun',
                // 'client' => [
                //     'timeout' => 5,
                // ],
            ],

            'roundrobin' => [
                'transport' => 'roundrobin',
                'mailers' => [
                    'ses',
                    'postmark',
                ],
            ],
            'smtp' => [
                'transport' => 'smtp',
                'scheme' => env('MAIL_SCHEME', null),
                'url' => env('MAIL_URL', null),
                'host' => env('MAIL_HOST', '127.0.0.1'),
                'port' => env('MAIL_PORT', 465),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
            ],
        ],

        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'noreply@yagoda.team'),
            'name' => env('MAIL_FROM_NAME', 'yagoda.team'),
        ],
        'to2' => [
            'address' => env('MAIL_TO_ADDRESS', 'mastera@yagoda.team'),
            'name' => env('MAIL_FROM_NAME', 'yagoda.team'),
        ],
        'to3' => [
            'address' => env('MAIL_TO_ADDRESS_2', 'mastera2@yagoda.team'),
            'name' => env('MAIL_FROM_NAME_2', 'yagoda.team'),
        ],

    ];
