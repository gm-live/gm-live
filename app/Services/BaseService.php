<?php
declare (strict_types = 1);

namespace App\Services;

use App\Exception\ExceptionCode as ExCode;

class BaseService
{

	public function getRedisTokenKey($sToken)
    {
        return sprintf(config('user.token_key'), $sToken);
    }
	
	public function getUserIdByToken($sToken)
	{
        $sTokenKey = $this->getRedisTokenKey($sToken);
        return $this->oRedis->get($sTokenKey);
	}

	public function checkTokenOrFail($sToken)
    {
        if (! $this->checkToken($sToken)) {
            ExCode::fire(ExCode::USER_TOKEN_ERROR);
        }
    }

    public function checkToken($sToken)
    {
        $sTokenKey = $this->getRedisTokenKey($sToken);
        return $this->oRedis->exists($sTokenKey);
    }
}
