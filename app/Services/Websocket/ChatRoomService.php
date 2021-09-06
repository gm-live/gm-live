<?php
declare (strict_types = 1);

namespace App\Services\Websocket;

use Hyperf\Di\Annotation\Inject;
use Exception;
use App\Repositories\UserRepo;
use Hyperf\Redis\Redis;
use App\Exception\ExceptionCode as ExCode;

class ChatRoomService extends BaseWebsocketService
{

	/**
	 * @Inject
	 * @var UserRepo
	 */
	protected $oUserRepo;

    public function joinRoom($oServer, $sToken, $iFd, $iRoomId = 1)
    {
        // 加入房間
        $iUserId = $this->getUserIdByToken($sToken);
        $oUser = $this->oUserRepo->findById($iUserId);

        // TODO 更多檢查
        if (! $oUser) {
            ExCode::fire(ExCode::USER_NOT_FOUND_ERROR);
        }
        
        // fd 綁定 user_id
        $this->bindFdAndUserId($iFd, $iUserId);

        // 加入房間
        $this->joinRoomByFd($iFd, $iUserId, $iRoomId);

        // 推送歡迎通知
        $sMsg = $oUser->username . ' joined room!';
        $aMsg = $this->makeMsg($iRoomId, $sMsg);
        $this->pushAllMsgByRoomId($oServer, $iRoomId, $aMsg);
    }

    public function leaveAllRoom($oServer, $iFd)
    {
        $aRoomIds = $this->getRoomIdsByFd($iFd);

        $iUserId = $this->getUserIdByFd($iFd);
        $oUser = $this->oUserRepo->findById($iUserId);

        // TODO 更多檢查
        if (! $oUser) {
            ExCode::fire(ExCode::USER_NOT_FOUND_ERROR);
        }

        $aMsg = $this->makeMsg($iRoomId, $oUser->username . ' leaved room!');
        foreach ($aRoomIds as $iRoomId => $_) {

            // 離開房間
            $this->leaveRoomByFd($iFd, $iUserId, $iRoomId);

            // 發出離開訊息
            $this->pushAllMsgByRoomId($oServer, $iRoomId, $aMsg);
        }

        // fd 解綁 user_id
        $this->unbindFdAndUserId($iFd, $iUserId);
    }

    public function leaveRoom($oServer, $iFd, $iRoomId)
    {
        $iUserId = $this->getUserIdByFd($iFd);
        $oUser = $this->oUserRepo->findById($iUserId);

        // TODO 更多檢查
        if (! $oUser) {
            ExCode::fire(ExCode::USER_NOT_FOUND_ERROR);
        }

        // 離開房間
        $this->leaveRoomByFd($iFd, $iUserId, $iRoomId);

        // 發出離開訊息
        $sMsg = $oUser->username . ' leaved room!';
        $aMsg = $this->makeMsg($iRoomId, $sMsg);
        $this->pushAllMsgByRoomId($oServer, $iRoomId, $aMsg);

    }

    public function handleMsg($oServer, $iFd, $aData)
    {
        $iRoomId  = $aData['room_id'];
        $iMsgType = $aData['msg_type'];
        $sMsg     = $aData['msg'];

        // TODO 持久化
        
        // 推送
        $aMsg = $this->makeMsg($iRoomId, $sMsg);
        $this->pushAllMsgByRoomId($oServer, $iRoomId, $aMsg);
    }

}
