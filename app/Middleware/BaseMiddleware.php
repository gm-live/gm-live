<?php

declare (strict_types = 1);

namespace App\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BaseMiddleware implements MiddlewareInterface
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $oContainer;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $oRequest;

    /**
     * @Inject
     * @var HttpResponse
     */
    protected $oResponse;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
