<?php
declare (strict_types = 1);

namespace App\Services\Websocket;

use App\Services\BaseService;
use Hyperf\Di\Annotation\Inject;
use App\Repositories\UserRepo;
use Hyperf\Server\ServerFactory;
use App\Constants\WebsocketConst as WsConst;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;

class BaseWebsocketService extends BaseService
{
    /**
     * @Inject
     * @var UserRepo
     */
    protected $oUserRepo;

    public function destroyAllFd()
    {
        $aAllFdRoomkeys = $this->getAllFdRoomkeys();
        $sFdKey = $this->getFdUserMapKey();
        $aAllFdRoomkeys[] = $sFdKey;
        $this->oRedis->del($aAllFdRoomkeys);
    }

    public function getAllRoomKeys()
    {
        $sRoomPfx = $this->getRoomKey('');
        return $this->oRedis->keys($sRoomPfx . '*');
    }

    public function getAllFdRoomkeys()
    {
        $sFdRoomPfx = $this->getFdRoomKey('');
        return $this->oRedis->keys($sFdRoomPfx . '*');
    }

    public function getRoomKey($iRoomId)
    {
        return sprintf(config('chatRoom.room_key'), $iRoomId);
    }

    public function getFdRoomKey($iFd)
    {
        return sprintf(config('chatRoom.fd_room_map_key'), $iFd);
    }

    public function bindFdAndUserId($iFd, $iUserId)
    {
        $sFdKey = $this->getFdUserMapKey();
        $this->oRedis->hset($sFdKey, (string) $iFd, $iUserId);
    }

    public function unbindFdAndUserId($iFd)
    {
        $sFdKey = $this->getFdUserMapKey();
        $this->oRedis->hdel($sFdKey, (string) $iFd);
    }

    public function getAllFdByRoomId($iRoomId)
    {
        $sRoomKey = $this->getRoomKey($iRoomId);
        return $this->oRedis->hgetall($sRoomKey);
    }

    public function pushAllMsgByRoomId($iRoomId, $aMsgData = [])
    {
        $aRoomAllFds = $this->getAllFdByRoomId($iRoomId);
        $oServer     = server();
        foreach ($aRoomAllFds as $iUserId => $iFd) {
            $oServer->push((int) $iFd, json_encode($aMsgData));
        }
    }

    public function makeMsg(
        $iRoomId,
        $sMsg = '',
        $oUser = null,
        $iMsgType = WsConst::MSG_TYPE_NORMAL,
        $iStatus = WsConst::WEBSOCKET_STATUS_OK
    ) {
        return [
            'status'   => $iStatus,
            'room_id'  => $iRoomId,
            'msg_type' => $iMsgType,
            'user_id'  => $oUser->id ?? '',
            'username' => $oUser->username ?? '',
            'avatar_url' => '', // TODO user 的聊天頭像
            'msg'      => $sMsg,
        ];
    }

    public function getRoomIdsByFd($iFd)
    {
        $sFdRoomKey = $this->getFdRoomKey($iFd);
        return $this->oRedis->hgetall($sFdRoomKey);
    }

    public function getUserOrFailByFd($iFd)
    {
        $iUserId = $this->getUserIdByFd($iFd);
        $oUser   = $this->oUserRepo->findById($iUserId);

        if (!$oUser) {
            throw new WorkException(Code::USER_NOT_FOUND_ERROR);
        }

        return $oUser;
    }

    public function getAllOnlineRoomIds(): array
    {
        $sRoomKey = $this->getRoomKey('*');
        $aOnlineRooms = $this->oRedis->keys($sRoomKey);
        foreach ($aOnlineRooms as &$iRoomId) {
            list(,$iRoomId) = explode(':', $iRoomId);
        }
        return $aOnlineRooms;
    }

    public function closeRoom($iRoomId)
    {
        $sRoomKey = $this->getRoomKey($iRoomId);
        $this->oRedis->del($sRoomKey);
    }

    public function openRoom($iRoomId)
    {
        $sRoomKey = $this->getRoomKey($iRoomId);
        $this->oRedis->hset($sRoomKey, 'streamer', -1);
    }



}
