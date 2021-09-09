<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class WebsocketConst extends AbstractConstants
{
	// 推送成功狀態碼
    const WEBSOCKET_STATUS_OK = 1;

    // 推送功能類型
    const MSG_TYPE_NORMAL = 1; // 一般聊天文字
    const MSG_TYPE_SYSTEM = 2; // 系統訊息文字
}