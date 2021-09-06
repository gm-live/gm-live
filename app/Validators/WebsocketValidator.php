<?php
declare (strict_types = 1);

namespace App\Validators;

use Exception;
use App\Exception\ExceptionCode as ExCode;

class WebsocketValidator extends AbstractValidator
{

    public function msgDataCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'room_id' => 'required|int',
                'msg_type' => 'required|int',
                'msg' => 'required|string',
            ],
            [
                'room_id.required' => 'room_id 為必填.',
                'room_id.int' => 'room_id 為數字.',
                'msg_type.required' => 'msg_type 為必填.',
                'msg_type.int' => 'msg_type 為數字.',
                'msg.required' => 'msg 為必填.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            ExCode::fire(ExCode::WEBSOCKET_DATA_FORMAT_ERROR, $sErrorMsg);
        }
    }

}
