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
		Router::addGroup('/user', function () {
			Router::get('/info', 'App\Controller\Api\UserController@info');
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
	Router::get('/check', 'App\Controller\Streamer\StreamController@check');
});

