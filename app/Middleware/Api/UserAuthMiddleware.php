<?php

declare (strict_types = 1);

namespace App\Middleware\Api;

use App\Middleware\BaseMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Services\Api\UserService;
use Hyperf\Di\Annotation\Inject;

class UserAuthMiddleware extends BaseMiddleware
{
    /**
     * @Inject
     * @var UserService
     */
    protected $oUserService;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $sToken = $this->oRequest->header('token', null);
        $this->oUserService->checkTokenOrFail($sToken);

        // 驗證成功 增加token時效
        $this->oUserService->addTokenExpireTime($sToken);

        return $handler->handle($request);
    }
}
