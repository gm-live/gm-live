<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware extends BaseMiddleware
{
    public function process(ServerRequestInterface $oRequest, RequestHandlerInterface $oHandler): ResponseInterface
    {
        $oResponse = Context::get(ResponseInterface::class);
        $oResponse = $oResponse->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization,token');

        Context::set(ResponseInterface::class, $oResponse);
        if ($oRequest->getMethod() == 'OPTIONS') {
            return $oResponse;
        }

        return $oHandler->handle($oRequest);
    }
}