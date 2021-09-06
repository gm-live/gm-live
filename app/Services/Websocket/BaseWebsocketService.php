<?php
declare (strict_types = 1);

namespace App\Services\Websocket;

use App\Services\BaseService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

class BaseWebsocketService extends BaseService
{
    const WEBSOCKET_STATUS_OK = 1;

	// 推送功能類型
    const MSG_TYPE_NORMAL = 1;  // 一般聊天文字

	/**
     * @Inject
     * @var Redis
     */
    protected $oRedis;

    public function getRoomKey($iRoomId)
    {
        return sprintf(config('chatRoom.room_key'), $iRoomId);
    }

    public function getFdUserMapKey()
    {
        return config('chatRoom.fd_user_id_map_key');
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

    public function makeMsg($iRoomId, $sMsg = '', $iMsgType = self::MSG_TYPE_NORMAL, $iStatus = self::WEBSOCKET_STATUS_OK)
    {
    	return [
            'status' => $iStatus,
            'room_id' => $iRoomId,
    		'msg_type' => $iMsgType,
    		'msg' => $sMsg,
    	];
    }

    public function getRoomIdsByFd($iFd)
    {
    	$sFdRoomKey = $this->getFdRoomKey($iFd);
    	return $this->oRedis->hgetall($sFdRoomKey);
    }

    public function getUserIdByFd($iFd)
    {
    	$sFdKey = $this->getFdUserMapKey();
    	return $this->oRedis->hget($sFdKey, (string)$iFd);
    }


}
