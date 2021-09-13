<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Hyperf\Logger\LoggerFactory;
use App\Validators\ChatRoomValidator;
use Hyperf\Di\Annotation\Inject;
use App\Services\Websocket\ChatRoomService;

class ChatRoomController extends AbstractController
{

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

	public function rooms()
    {
        $iUserId = $this->getUserId();
    	$aRooms = $this->oChatRoomService->getOnlineRooms($iUserId);
   		return $this->success(['rooms' => $aRooms]);
    }

}
