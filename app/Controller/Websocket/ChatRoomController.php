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
use App\Validators\ChatRoomValidator;
use App\Services\Api\UserService;
use App\Constants\ChatRoomConst;
use Hyperf\Logger\LoggerFactory;


class ChatRoomController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{

    protected $oLogger = null;

    /**
     * @Inject
     * @var ChatRoomService
     */
    protected $oChatRoomService;

    /**
     * @Inject
     * @var ChatRoomValidator
     */
    protected $oChatRoomValidator;

    /**
     * @Inject
     * @var UserService
     */
    protected $oUserService;

    public function __construct(LoggerFactory $oLoggerFactory)
    {
        $this->oLogger = $oLoggerFactory->get();
    }

    public function onOpen($oServer, Request $oRequest): void
    {

        try {
            
            $iFd = $oRequest->fd;

            $this->oChatRoomValidator->openDataCheck($oRequest->get);

            $sToken = $oRequest->get['token'];
            $iRoomId = $oRequest->get['room_id'] ?? null; 
            
            // 加入房間
            $this->oChatRoomService->joinRoom($sToken, $iFd, $iRoomId);

            // 推房間歷史最新的幾條數據
            $this->oChatRoomService->pushLastMsgs($iFd, $iRoomId);

        } catch (Throwable $e) {
            $oServer->disconnect($iFd, SWOOLE_WEBSOCKET_CLOSE_NORMAL, $e->getMessage());
        }
    }

    public function onMessage($oServer, Frame $oFrame): void
    {
        try {

            $iFd = $oFrame->fd;
            $jData = $oFrame->data;

            $this->oLogger->info("fd = $iFd, message = $jData");

            // 延長token時間
            $this->oUserService->addTokenExpireTimeByFd($iFd);

            // 驗證格式
            $aData = json_decode($jData, true) ?? [];
            $iRoomId = $aData['room_id'] ?? null;
            $this->oChatRoomValidator->msgDataCheck($aData);
            $this->oChatRoomService->handleMsg($iFd, $aData);

        } catch (Throwable $e) {
            if ($iRoomId) {
                $aMsgData = $this->oChatRoomService->makeMsg(
                    $iRoomId,
                    $e->getMessage(), 
                    null,
                    ChatRoomConst::MSG_TYPE_SYSTEM, 
                    $e->getCode()
                );
                $oServer->push((int)$iFd, json_encode($aMsgData, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function onClose($oServer, int $iFd, int $reactorId): void
    {
        $this->oChatRoomService->leaveAllRoom($iFd);
    }

}
