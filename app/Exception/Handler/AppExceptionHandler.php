<?php

declare (strict_types = 1);

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use App\Exception\WorkException;
use App\Constants\ErrorCode;
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

        if ($oThrowable instanceof WorkException) {
            return $oHyperfResponse->json([
                'status' => $iErrCode,
                'msg'    => $sMsg ?? ErrorCode::getMessage($iErrCode),
                'data'   => [],
            ]);
        } else {
            return $oHyperfResponse->json([
                'status' => ErrorCode::SYSTEM_OTHER_ERROR,
                'msg'    => ErrorCode::getMessage(ErrorCode::SYSTEM_OTHER_ERROR),
                'data'   => [],
            ]);
        }
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
