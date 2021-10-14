<?php

declare (strict_types = 1);

use Hyperf\Server\Event;
use Hyperf\Server\Server;
use Swoole\Constant;

$sDefaultPidPath = BASE_PATH . '/runtime/hyperf.pid';

return [
    'mode'      => SWOOLE_PROCESS,
    'servers'   => [
        [
            'name'      => 'http',
            'type'      => Server::SERVER_WEBSOCKET,
            'host'      => '0.0.0.0',
            'port'      => (int) env('SERVER_PORT', 9501),
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_MANAGER_START => [App\ServerListener\OnManagerStartListener::class, 'listen'],
                Event::ON_REQUEST       => [Hyperf\HttpServer\Server::class, 'onRequest'],
                Event::ON_HAND_SHAKE    => [Hyperf\WebSocketServer\Server::class, 'onHandShake'],
                Event::ON_MESSAGE       => [Hyperf\WebSocketServer\Server::class, 'onMessage'],
                Event::ON_CLOSE         => [Hyperf\WebSocketServer\Server::class, 'onClose'],
            ],
        ],
    ],
    'settings'  => [
        Constant::OPTION_ENABLE_COROUTINE    => true,
        Constant::OPTION_WORKER_NUM          => swoole_cpu_num() * 2,
        Constant::OPTION_PID_FILE            => env('PID_PATH', 'default') == 'default' ?  $sDefaultPidPath: env('PID_PATH'),
        Constant::OPTION_OPEN_TCP_NODELAY    => true,
        Constant::OPTION_MAX_COROUTINE       => 100000,
        Constant::OPTION_OPEN_HTTP2_PROTOCOL => true,
        Constant::OPTION_MAX_REQUEST         => 100000,
        Constant::OPTION_SOCKET_BUFFER_SIZE  => 2 * 1024 * 1024,
        Constant::OPTION_BUFFER_OUTPUT_SIZE  => 2 * 1024 * 1024,
    ],
    'callbacks' => [
        Event::ON_WORKER_START => [Hyperf\Framework\Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
        Event::ON_PIPE_MESSAGE => [Hyperf\Framework\Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        Event::ON_WORKER_EXIT  => [Hyperf\Framework\Bootstrap\WorkerExitCallback::class, 'onWorkerExit'],
    ],
];
