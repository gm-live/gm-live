<?php
declare (strict_types = 1);

namespace App\Validators;

use Exception;
use App\Exception\ExceptionCode as ExCode;

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
            ExCode::fire(ExCode::USER_LOGIN_PARAMATER_ERROR);
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
            ExCode::fire(ExCode::USER_LOGIN_PARAMATER_ERROR);
        }
    }

}
