<?php
declare (strict_types = 1);

namespace App\Controller\Streamer;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

class StreamController extends AbstractController
{
	protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get();
    }

    public function check()
    {
        $this->logger->info("Rtmps: Param:", $this->oRequest->all());
        
        // 串流金鑰
        $sLiveToken = $this->oRequest->input('name', null);

        if ($sLiveToken == '123') {
            return $this->oResponse->raw("")->withStatus(202);
        }

        return $this->oResponse->raw("")->withStatus(403);
    }
}
