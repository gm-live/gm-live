<?php

declare (strict_types = 1);

namespace App\Exception\Handler;

use App\Exception\ExceptionCode as ExCode;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $oThrowable, ResponseInterface $oResponse)
    {
        // log
        $this->logger->error(sprintf('%s[%s] in %s', $oThrowable->getMessage(), $oThrowable->getLine(), $oThrowable->getFile()));
        $this->logger->error($oThrowable->getTraceAsString());

        // response
        $iErrCode = $oThrowable->getCode();
        $sMsg     = $oThrowable->getMessage();

        $oHyperfResponse = new \Hyperf\HttpServer\Response($oResponse);

        if (isset(ExCode::EX_MSGS[$iErrCode])) {
            return $oHyperfResponse->json([
                'status' => $iErrCode,
                'msg'    => $sMsg ?? ExCode::EX_MSGS[$iErrCode],
                'data'   => [],
            ]);
        } else {
            return $oHyperfResponse->json([
                'status' => ExCode::SYSTEM_OTHER_ERROR,
                'msg'    => ExCode::EX_MSGS[ExCode::SYSTEM_OTHER_ERROR],
                'data'   => [],
            ]);
        }
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
