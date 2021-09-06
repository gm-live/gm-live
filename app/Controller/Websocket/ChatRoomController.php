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

class ChatRoomController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{

    /**
     * @Inject
     * @var ChatRoomService
     */
    protected $oChatRoomService;

    public function onOpen($oServer, Request $oRequest): void
    {
        $sToken = $oRequest->header['token'];
        $iRoomId = $oRequest->get['room_id'] ?? 1;  // 預設1號房間 
        $this->oChatRoomService->joinRoom($oServer, $sToken, $oRequest->fd, $iRoomId);
    }

    public function onMessage($oServer, Frame $oFrame): void
    {
        $iFd = $frame->fd;
        $aData = $oFrame->data;
        $this->oChatRoomService->handleMsg($oServer, $iFd, $aData);
    }

    public function onClose($oServer, int $iFd, int $reactorId): void
    {
        $this->oChatRoomService->leaveAllRoom($oServer, $iFd);
    }

}
