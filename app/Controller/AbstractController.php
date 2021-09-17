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
namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use App\Services\Api\UserService;
use App\Constants\HttpConst;
use Hyperf\Logger\LoggerFactory;

abstract class AbstractController
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
     * @var ResponseInterface
     */
    protected $oResponse;

    /**
     * @Inject
     * @var UserService
     */
    protected $oUserService;

    protected $oLogger;

    public function __construct(LoggerFactory $oLoggerFactory)
    {
        $this->oLogger = $oLoggerFactory->get();
    }

    public function success($aData = [])
    {
        return [
            'status' => HttpConst::STATUS_OK,
            'msg' => 'success',
            'data' => $aData,
        ];
    }

    public function getUserId()
    {
        $sToken = $this->oRequest->header('token');
        return $this->oUserService->getUserIdByToken($sToken);
    }
}
