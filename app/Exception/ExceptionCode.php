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

    const EX_MSGS = [
        self::SYSTEM_OTHER_ERROR                    => '未知錯誤',
        self::USER_LOGIN_PARAMATER_ERROR            => '登入,參數錯誤',
        self::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR => '用戶名或密碼錯誤',
        self::USER_CREATE_ERROR                     => '用戶建立失敗',
        self::USER_USERNAME_REPEAT_ERROR            => '用戶名重複',
    ];

    public static function fire($iCode, $sMsg = null)
    {
        throw new Exception($sMsg ?? self::EX_MSGS[$iCode], $iCode);
    }

}
