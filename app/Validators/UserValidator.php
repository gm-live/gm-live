<?php
declare (strict_types = 1);

namespace App\Validators;

use Exception;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;

class UserValidator extends AbstractValidator
{

    public function userRegisterCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'username.required' => 'username 為必填.',
                'password.required' => 'password 為必填.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::USER_REGISTER_PARAMTER_ERROR, $sErrorMsg);
        }
    }

    public function userLoginCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'username.required' => 'username 為必填.',
                'password.required' => 'password 為必填.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::USER_LOGIN_PARAMATER_ERROR, $sErrorMsg);
        }
    }

}
