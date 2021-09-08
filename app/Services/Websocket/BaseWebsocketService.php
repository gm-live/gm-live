<?php
declare (strict_types = 1);

namespace App\Services\Websocket;

use App\Services\BaseService;
use Hyperf\Di\Annotation\Inject;
use App\Repositories\UserRepo;
use App\Exception\ExceptionCode as ExCode;

class BaseWebsocketService extends BaseService
{
    const WEBSOCKET_STATUS_OK = 1;

	// 推送功能類型
    const MSG_TYPE_NORMAL = 1;  // 一般聊天文字
    const MSG_TYPE_SYSTEM = 2;  // 系統訊息文字

    /**
     * @Inject
     * @var UserRepo
     */
    protected $oUserRepo;

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
    	$this->oRedis->hset($sFdKey, (string)$iFd, $iUserId);
    }

    public function unbindFdAndUserId($iFd)
    {
    	$sFdKey = $this->getFdUserMapKey();
    	$this->oRedis->hdel($sFdKey, (string)$iFd);
    }

    public function joinRoomByFd($iFd, $iUserId, $iRoomId)
    {
    	$sRoomKey = $this->getRoomKey($iRoomId);
    	$sFdRoomKey = $this->getFdRoomKey($iFd);
    	$this->oRedis->hset($sRoomKey, (string)$iUserId, $iFd);
    	$this->oRedis->hset($sFdRoomKey, (string)$iRoomId, $iUserId);
    }

    public function leaveRoomByFd($iFd, $iUserId, $iRoomId)
    {
    	$sRoomKey = $this->getRoomKey($iRoomId);
    	$sFdRoomKey = $this->getFdRoomKey($iFd);
    	$this->oRedis->hdel($sRoomKey, (string)$iUserId);
    	$this->oRedis->hdel($sFdRoomKey, (string)$iRoomId);
    }

    public function getAllFdByRoomId($iRoomId)
    {
    	$sRoomKey = $this->getRoomKey($iRoomId);
    	return $this->oRedis->hgetall($sRoomKey);
    }

    public function pushAllMsgByRoomId($oServer, $iRoomId, $aMsgData = [])
    {
    	$aRoomAllFds = $this->getAllFdByRoomId($iRoomId);
        foreach ($aRoomAllFds as $iUserId => $iFd) {
            $oServer->push((int)$iFd, json_encode($aMsgData));
        }
    }

    public function makeMsg(
        $iRoomId, 
        $sMsg = '', 
        $oUser = null,
        $iMsgType = self::MSG_TYPE_NORMAL, 
        $iStatus = self::WEBSOCKET_STATUS_OK
    ) {
    	return [
            'status' => $iStatus,
            'room_id' => $iRoomId,
    		'msg_type' => $iMsgType,
            'user_id' => $oUser->id ?? '',
            'username' => $oUser->username ?? '',
    		'msg' => $sMsg,
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
        $oUser = $this->oUserRepo->findById($iUserId);

        if (! $oUser) {
            ExCode::fire(ExCode::USER_NOT_FOUND_ERROR);
        }

        return $oUser;
    }


}
