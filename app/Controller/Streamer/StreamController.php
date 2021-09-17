<?php
declare (strict_types = 1);

namespace App\Controller\Streamer;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use App\Services\Websocket\ChatRoomService;
use Hyperf\Di\Annotation\Inject;
use Throwable;

class StreamController extends AbstractController
{
	protected $oLogger;

    /**
     * @Inject
     * @var ChatRoomService
     */
    protected $oChatRoomService;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->oLogger = $loggerFactory->get();
    }

    public function openRoom()
    {
        $this->oLogger->info("open Rtmps: Param:", $this->oRequest->all());
        
        // 串流金鑰
        $iRoomId = $this->oRequest->input('name', null);

        try {

            // TODO 開房驗證機制
            if ($iRoomId == '1') {
                $this->oChatRoomService->openRoom($iRoomId);
                return $this->oResponse->raw("")->withStatus(202);
            }
        } catch (Throwable $e) {
            $this->oLogger->info('open Rtmps: error: ' . $e->getMessage());
        }
        
        return $this->oResponse->raw("")->withStatus(403);
    }

    public function closeRoom()
    {
        $this->oLogger->info("close Rtmps: Param:", $this->oRequest->all());

        // 串流金鑰
        $iRoomId = $this->oRequest->input('name', null);

        try {
            $this->oChatRoomService->closeRoom($iRoomId);
        } catch (Throwable $e) {
            $this->oLogger->info('close Rtmps: error: ' . $e->getMessage());
        }

    }
}
