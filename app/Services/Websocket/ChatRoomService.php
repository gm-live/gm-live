<?php
declare (strict_types = 1);

namespace App\Services\Websocket;

use Hyperf\Di\Annotation\Inject;
use Exception;
use Hyperf\Redis\Redis;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;
use App\Repositories\ConfigRepo;

class ChatRoomService extends BaseWebsocketService
{

    /**
     * @Inject
     * @var ConfigRepo
     */
    protected $oConfigRepo;

    public function joinRoom($sToken, $iFd, $iRoomId)
    {
        $iUserId = $this->getUserIdByToken($sToken);
        $oUser = $this->oUserRepo->findById($iUserId);

        // TODO 更多檢查
        if (! $oUser) {
            throw new WorkException(Code::USER_NOT_FOUND_ERROR);
        }
        
        // fd 綁定 user_id
        $this->bindFdAndUserId($iFd, $iUserId);

        // 加入房間
        $this->joinRoomByFd($iFd, $iUserId, $iRoomId);

        // 推送歡迎通知
        $sMsg = $oUser->username . ' joined room!';
        $aMsg = $this->makeMsg($iRoomId, $sMsg, $oUser);
        $this->pushAllMsgByRoomId($iRoomId, $aMsg);
    }

    public function leaveAllRoom($iFd)
    {
        $aRoomIds = $this->getRoomIdsByFd($iFd);
        $oUser = $this->getUserOrFailByFd($iFd);

        $sMsg = $oUser->username . ' leaved room!';
        $aMsg = $this->makeMsg($iRoomId, $sMsg, $oUser);
        foreach ($aRoomIds as $iRoomId => $_) {

            // 離開房間
            $this->leaveRoomByFd($iFd, $oUser->id, $iRoomId);

            // 發出離開訊息
            $this->pushAllMsgByRoomId($iRoomId, $aMsg);
        }

        // fd 解綁 user_id
        $this->unbindFdAndUserId($iFd, $oUser->id);
    }

    public function leaveRoom($iFd, $iRoomId)
    {
        $oUser = $this->getUserOrFailByFd($iFd);

        // 離開房間
        $this->leaveRoomByFd($iFd, $oUser->id, $iRoomId);

        // 發出離開訊息
        $sMsg = $oUser->username . ' leaved room!';
        $aMsg = $this->makeMsg($iRoomId, $sMsg, $oUser);
        $this->pushAllMsgByRoomId($iRoomId, $aMsg);

    }

    public function joinRoomByFd($iFd, $iUserId, $iRoomId)
    {
        $sRoomKey   = $this->getRoomKey($iRoomId);
        $iHasRoom = $this->oRedis->exists($sRoomKey);
        if (! $iHasRoom) {
            throw new WorkException(Code::CHAT_ROOM_NOT_EXISTS);
        }
        $sFdRoomKey = $this->getFdRoomKey($iFd);
        $this->oRedis->hset($sRoomKey, (string) $iUserId, $iFd);
        $this->oRedis->hset($sFdRoomKey, (string) $iRoomId, $iUserId);
    }

    public function leaveRoomByFd($iFd, $iUserId, $iRoomId)
    {
        $sRoomKey   = $this->getRoomKey($iRoomId);
        $sFdRoomKey = $this->getFdRoomKey($iFd);
        $this->oRedis->hdel($sRoomKey, (string) $iUserId);
        $this->oRedis->hdel($sFdRoomKey, (string) $iRoomId);
    }


    public function handleMsg($iFd, $aData)
    {
        $iRoomId  = $aData['room_id'];
        $iMsgType = $aData['msg_type'];
        $sMsg     = $aData['msg'];
        $oUser    = $this->getUserOrFailByFd($iFd);

        // TODO 持久化
        
        // 推送
        $aMsg = $this->makeMsg($iRoomId, $sMsg, $oUser);
        $this->pushAllMsgByRoomId($iRoomId, $aMsg);
    }

    public function pushLastMsgs($iFd, $iRoomId)
    {
         // TODO 推歷史資料   
    }

    public function getOnlineRooms($iUserId = null)
    {
        $aRooms = [];

        if ($iUserId) {
            // TODO user_id預留 可用於檢核
        }
        
        $aAllRoomsIds = $this->getAllOnlineRoomIds();

        // TODO 主播資訊模組
        $sHlsServerDomain = $this->oConfigRepo->getValueByName('hls_server_domain');
        $sVideoName = 'ppg.m3u8';
        $aHardCodeData = [
            '1' => [
                'room_id' => '1',
                'room_pic' => '',
                'streamer_id' => 1,
                'name' => '派派哥',
                'video_url' => $sHlsServerDomain . $sVideoName,
            ],
        ];

        if (in_array('1', $aAllRoomsIds)) {
            $aRooms[] = $aHardCodeData['1'];
        }

        return $aRooms;

    }

}
