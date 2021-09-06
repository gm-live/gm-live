<?php
declare(strict_types=1);

namespace App\Controller\Websocket;

use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;
use Hyperf\Di\Annotation\Inject;
use App\Services\Websocket\ChatRoomService;
use Throwable;
use App\Exception\ExceptionCode as ExCode;
use App\Validators\WebsocketValidator;


class ChatRoomController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{

    /**
     * @Inject
     * @var ChatRoomService
     */
    protected $oChatRoomService;

    /**
     * @Inject
     * @var WebsocketValidator
     */
    protected $oWebsocketValidator;

    public function onOpen($oServer, Request $oRequest): void
    {
        $sToken = $oRequest->header['token'];
        $iRoomId = $oRequest->get['room_id'] ?? 1;  // 預設1號房間 
        $this->oChatRoomService->joinRoom($oServer, $sToken, $oRequest->fd, $iRoomId);
    }

    public function onMessage($oServer, Frame $oFrame): void
    {
        try {
            
            $iFd = $oFrame->fd;
            $jData = $oFrame->data;

            // 驗證格式
            $aData = json_decode($jData, true) ?? [];
            $this->oWebsocketValidator->msgDataCheck($aData);
            $this->oChatRoomService->handleMsg($oServer, $iFd, $aData);

        } catch (Throwable $e) {
            $aMsgData = $this->oChatRoomService->makeMsg(
                $e->getMessage(), 
                $this->oChatRoomService::MSG_TYPE_NORMAL, 
                $e->getCode()
            );
            $oServer->push((int)$iFd, json_encode($aMsgData, JSON_UNESCAPED_UNICODE));
        }
    }

    public function onClose($oServer, int $iFd, int $reactorId): void
    {
        $this->oChatRoomService->leaveAllRoom($oServer, $iFd);
    }

}
