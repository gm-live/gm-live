<?php
declare (strict_types = 1);

namespace App\Validators;

use Exception;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;

class ChatRoomValidator extends AbstractValidator
{

    public function msgDataCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'room_id' => 'required|int',
                'msg_type' => 'required|int',
                'present_type' => 'int',
                'msg' => 'required|string',
            ],
            [
                'room_id.required' => 'room_id 為必填.',
                'room_id.int' => 'room_id 為數字.',
                'msg_type.required' => 'msg_type 為必填.',
                'msg_type.int' => 'msg_type 為數字.',
                'present_type.int' => 'present_type 為數字.',
                'msg.required' => 'msg 為必填.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::WEBSOCKET_DATA_FORMAT_ERROR, $sErrorMsg);
        }
    }

    public function openDataCheck($aParams)
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
            throw new WorkException(Code::WEBSOCKET_DATA_FORMAT_ERROR, $sErrorMsg);
        }
    }

}
