<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;
use App\Middleware\Api\UserAuthMiddleware;


// 前台API
Router::addGroup('/api',function () {

	// 無須登入
	Router::addGroup('/user', function () {
		Router::post('/register', 'App\Controller\Api\UserController@register');
		Router::post('/login', 'App\Controller\Api\UserController@login');
	});

	// 需登入
	Router::addGroup('', function () {

		// 用戶
		Router::addGroup('/user', function () {
			Router::get('/info', 'App\Controller\Api\UserController@info');
			Router::post('/refresh-token', 'App\Controller\Api\UserController@refreshToken');
			Router::post('/logout', 'App\Controller\Api\UserController@logout');
		});

		// 聊天室API
		Router::addGroup('/chat-rooms', function () {
			Router::get('', 'App\Controller\Api\ChatRoomController@rooms');
		});


	}, 
	[
		'middleware' => [
			UserAuthMiddleware::class, 
		],
	]);
});


// 串流推向主機專用API
Router::addGroup('/streamer-api',function (){
	Router::get('/open-room', 'App\Controller\Streamer\StreamController@openRoom');
	Router::get('/close-room', 'App\Controller\Streamer\StreamController@closeRoom');
});


// websocket
Router::get('/', 'App\Controller\Websocket\ChatRoomController', [
	'middleware' => [
		UserAuthMiddleware::class, 
	],
]);

