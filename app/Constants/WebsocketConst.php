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
}