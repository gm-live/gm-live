<?php

declare(strict_types=1);

namespace App\ServerListener;

use Hyperf\Di\Annotation\Inject;
use App\Services\Websocket\ChatRoomService;

class OnManagerStartListener 
{
    /**
     * @Inject
     * @var ChatRoomService
     */
    protected $oChatRoomService;

    public function handle()
    {
        $this->oChatRoomService->destroyAllChatRoom();
        $this->oChatRoomService->destroyAllFd();
    }
    
}
