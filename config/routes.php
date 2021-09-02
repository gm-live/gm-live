<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;


// 前台API
Router::addGroup('/api',function (){
	Router::post('/login', 'App\Controller\Api\UserController@login');
});


// 串流推向主機專用API
Router::addGroup('/stream-api',function (){
	Router::get('/check', 'App\Controller\Stream\StreamController@check');
});

