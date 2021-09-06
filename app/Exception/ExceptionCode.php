<?php
declare (strict_types = 1);

namespace App\Exception;

use Exception;

class ExceptionCode
{
    const SYSTEM_OTHER_ERROR                    = 1000; // 未知錯誤
    const USER_LOGIN_PARAMATER_ERROR            = 1001; // 登入參數錯誤
    const USER_LOGIN_USERNAME_OR_PASSWORD_ERROR = 1002; // 密碼或用戶名錯誤
    const USER_CREATE_ERROR                     = 1003; // 用戶建立失敗
    const USER_USERNAME_REPEAT_ERROR            = 1004; // 用戶名重複
    const USER_TOKEN_ERROR                      = 1005; // token 無效
    const USER_NOT_FOUND_ERROR                  = 1006; // 無此用戶


    const WEBSOCKET_DATA_FORMAT_ERROR = 2001; // socket 資料格式錯誤

    const EX_MSGS = [
        self::SYSTEM_OTHER_ERROR                    => '未知錯誤',
        self::USER_LOGIN_PARAMATER_ERROR            => '登入,參數錯誤',
        self::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR => '用戶名或密碼錯誤',
        self::USER_CREATE_ERROR                     => '用戶建立失敗',
        self::USER_USERNAME_REPEAT_ERROR            => '用戶名重複',
        self::USER_TOKEN_ERROR                      => 'token 無效',
        self::USER_NOT_FOUND_ERROR                  => '無此用戶',
        self::WEBSOCKET_DATA_FORMAT_ERROR           => '資料格式錯誤',
    ];

    public static function fire($iCode, $sMsg = null)
    {
        throw new Exception($sMsg ?? self::EX_MSGS[$iCode], $iCode);
    }

}
