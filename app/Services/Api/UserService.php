<?php
declare (strict_types = 1);

namespace App\Services\Api;

use Hyperf\Di\Annotation\Inject;
use Exception;
use App\Repositories\UserRepo;
use Hyperf\Redis\Redis;
use App\Exception\ExceptionCode as ExCode;


class UserService
{

	/**
	 * @Inject
	 * @var UserRepo
	 */
	protected $oUserRepo;


    /**
     * @Inject
     * @var Redis
     */
    protected $oRedis;


    public function register($sUsername, $sPasssword)
    {
        $oUser = $this->oUserRepo->findByUsername($sUsername);
        if ($oUser) {
            ExCode::fire(ExCode::USER_USERNAME_REPEAT_ERROR);
        }

        $sHashPwd = $this->getPasswordHash($sUsername, $sPasssword);
        $oUser = $this->oUserRepo->create($sUsername, $sHashPwd);
        $sToken = $this->getToken($oUser);
        $this->setToken($sToken, $oUser->id);
        return $sToken;
    }

    public function login($sUsername, $sPasssword)
    {
    	$oUser = $this->oUserRepo->findByUsername($sUsername);
        if (! $oUser) {
            ExCode::fire(ExCode::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR);
        }

        $sHashPwd = $this->getPasswordHash($sUsername, $sPasssword);
        if ($sHashPwd != $oUser->password) {
            ExCode::fire(ExCode::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR);
        }

        $sToken = $this->getToken($oUser);

        $this->flushToken($oUser->id);
        $this->setToken($sToken, $oUser->id);

   		return $sToken;
    }

    public function getPasswordHash($sUsername, $sPasssword)
    {
        return hash('sha512', $sUsername . $sPasssword . md5($sPasssword));
    }

    public function getToken($oUser)
    {
        return hash('sha512', $oUser->id . $oUser->username . md5((string)microtime(true)));
    }

    public function setToken($sToken, $iUserId)
    {
        $sTokenKey = $this->getRedisTokenKey($sToken);
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $this->oRedis->setex($sTokenKey, config('user.token_expire_time'), $iUserId);
        $this->oRedis->setex($sUserIdTokenKey, config('user.token_expire_time'), $sTokenKey);
    }

    public function flushToken($iUserId)
    {
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $sToken = $this->oRedis->get($sUserIdTokenKey);
        if ($sToken) {
            $this->oRedis->del($sToken);
            $this->oRedis->del($sUserIdTokenKey);
        }
    }

    public function getRedisTokenKey($sToken)
    {
        return sprintf(config('user.token_key'), $sToken);
    }

    public function getRedisUserIdTokenKey($iUserId)
    {
        return sprintf(config('user.user_id_token_key'), $iUserId);
    }


}
