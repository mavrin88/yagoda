<?php

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

return [

    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['daily'],
                'ignore_exceptions' => false,
            ],

            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/laravel.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => env('LOG_MAX_FILES', 10),
            ],
        ],
    ],

];
