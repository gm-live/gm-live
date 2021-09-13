<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("未知錯誤")
     */
    const SYSTEM_OTHER_ERROR                    = 1000; 

    /**
     * @Message("登入參數錯誤")
     */
    const USER_LOGIN_PARAMATER_ERROR            = 1001; 

    /**
     * @Message("密碼或用戶名錯誤")
     */
    const USER_LOGIN_USERNAME_OR_PASSWORD_ERROR = 1002; 

    /**
     * @Message("用戶建立失敗")
     */
    const USER_CREATE_ERROR                     = 1003; 

    /**
     * @Message("用戶名重複")
     */
    const USER_USERNAME_REPEAT_ERROR            = 1004; 

    /**
     * @Message("token 無效")
     */
    const USER_TOKEN_ERROR                      = 1005; 

    /**
     * @Message("無此用戶")
     */
    const USER_NOT_FOUND_ERROR                  = 1006;

    /**
     * @Message("註冊參數錯誤")
     */
    const USER_REGISTER_PARAMTER_ERROR          = 1007; 

    /**
     * @Message("房間不存在")
     */
    const CHAT_ROOM_NOT_EXISTS                  = 1008; 




    /**
     * @Message("socket 資料格式錯誤")
     */
    const WEBSOCKET_DATA_FORMAT_ERROR = 2001; 


}
