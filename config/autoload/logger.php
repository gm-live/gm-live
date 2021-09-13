<?php

declare(strict_types=1);

$sDefaultLogPath = BASE_PATH . '/runtime/logs/hyperf.log';

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => env('LOG_PATH', 'default') == 'default' ? $sDefaultLogPath : env('LOG_PATH'),
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\JsonFormatter::class,
            'constructor' => [],
        ],
    ],
];
