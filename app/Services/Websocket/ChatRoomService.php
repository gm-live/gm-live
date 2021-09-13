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

    public function joinRoom($sToken, $iFd, $sRoomId)
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
        $this->joinRoomByFd($iFd, $iUserId, $sRoomId);

        // 推送歡迎通知
        $sMsg = $oUser->username . ' joined room!';
        $aMsg = $this->makeMsg($sRoomId, $sMsg, $oUser);
        $this->pushAllMsgByRoomId($sRoomId, $aMsg);
    }

    public function leaveAllRoom($iFd)
    {
        $aRoomIds = $this->getRoomIdsByFd($iFd);
        $oUser = $this->getUserOrFailByFd($iFd);

        $sMsg = $oUser->username . ' leaved room!';
        $aMsg = $this->makeMsg($sRoomId, $sMsg, $oUser);
        foreach ($aRoomIds as $sRoomId => $_) {

            // 離開房間
            $this->leaveRoomByFd($iFd, $oUser->id, $sRoomId);

            // 發出離開訊息
            $this->pushAllMsgByRoomId($sRoomId, $aMsg);
        }

        // fd 解綁 user_id
        $this->unbindFdAndUserId($iFd, $oUser->id);
    }

    public function leaveRoom($iFd, $sRoomId)
    {
        $oUser = $this->getUserOrFailByFd($iFd);

        // 離開房間
        $this->leaveRoomByFd($iFd, $oUser->id, $sRoomId);

        // 發出離開訊息
        $sMsg = $oUser->username . ' leaved room!';
        $aMsg = $this->makeMsg($sRoomId, $sMsg, $oUser);
        $this->pushAllMsgByRoomId($sRoomId, $aMsg);

    }

    public function joinRoomByFd($iFd, $iUserId, $sRoomId)
    {
        $sRoomKey   = $this->getRoomKey($sRoomId);
        $sFdRoomKey = $this->getFdRoomKey($iFd);
        $this->oRedis->hset($sRoomKey, (string) $iUserId, $iFd);
        $this->oRedis->hset($sFdRoomKey, (string) $sRoomId, $iUserId);
    }

    public function leaveRoomByFd($iFd, $iUserId, $sRoomId)
    {
        $sRoomKey   = $this->getRoomKey($sRoomId);
        $sFdRoomKey = $this->getFdRoomKey($iFd);
        $this->oRedis->hdel($sRoomKey, (string) $iUserId);
        $this->oRedis->hdel($sFdRoomKey, (string) $sRoomId);
    }


    public function handleMsg($iFd, $aData)
    {
        $sRoomId  = $aData['room_id'];
        $iMsgType = $aData['msg_type'];
        $sMsg     = $aData['msg'];
        $oUser    = $this->getUserOrFailByFd($iFd);

        // TODO 持久化
        
        // 推送
        $aMsg = $this->makeMsg($sRoomId, $sMsg, $oUser);
        $this->pushAllMsgByRoomId($sRoomId, $aMsg);
    }

    public function pushLastMsgs($iFd, $sRoomId)
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
