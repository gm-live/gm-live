<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ChatRoomConst extends AbstractConstants
{
    // 推送功能類型
    const MSG_TYPE_NORMAL = 1; // 一般聊天文字
    const MSG_TYPE_SYSTEM = 2; // 系統訊息文字
    const MSG_TYPE_PRESENT = 3; // 禮物訊息


    // TODO 後續將調成DB可配
    // 禮物類型 
    const PRESENT_TYPE_ROCKET = 1; // 火箭


    const ALL_PRESENTS = [
    	self::PRESENT_TYPE_ROCKET,
    ];
}