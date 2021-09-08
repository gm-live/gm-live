<?php
declare (strict_types = 1);

namespace App\Validators;

use Exception;
use App\Exception\ExceptionCode as ExCode;

class VideoValidator extends AbstractValidator
{
    public function getVideoOriginCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'room_id' => 'required|int',
            ],
            [
                'room_id.required' => 'room_id 為必填.',
                'room_id.int' => 'room_id 為數字.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            ExCode::fire(ExCode::GET_VIDEO_URL_PARAMTER_ERROR, $sErrorMsg);
        }
    }

}