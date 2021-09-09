<?php
declare (strict_types = 1);

namespace App\Services\Api;

use Hyperf\Di\Annotation\Inject;
use Exception;
use App\Repositories\UserRepo;
use Hyperf\Redis\Redis;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;

class UserService extends BaseApiService
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
            throw new WorkException(Code::USER_USERNAME_REPEAT_ERROR);
        }

        $sHashPwd = $this->makePasswordHash($sUsername, $sPasssword);
        $oUser    = $this->oUserRepo->create($sUsername, $sHashPwd);
        $sToken   = $this->makeToken($oUser);
        $this->setToken($sToken, $oUser->id);
        return $sToken;
    }

    public function login($sUsername, $sPasssword)
    {
        $oUser = $this->oUserRepo->findByUsername($sUsername);
        if (!$oUser) {
            throw new WorkException(Code::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR);
        }

        $sHashPwd = $this->makePasswordHash($sUsername, $sPasssword);
        if ($sHashPwd != $oUser->password) {
            throw new WorkException(Code::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR);
        }

        $sToken = $this->makeToken($oUser);

        $this->flushToken($oUser->id);
        $this->setToken($sToken, $oUser->id);

        return $sToken;
    }

    public function makePasswordHash($sUsername, $sPasssword)
    {
        return hash('sha512', $sUsername . $sPasssword . md5($sPasssword));
    }

    public function makeToken($oUser)
    {
        return hash('sha512', $oUser->id . $oUser->username . md5((string) microtime(true)));
    }

    public function setToken($sToken, $iUserId)
    {
        $sTokenKey       = $this->getRedisTokenKey($sToken);
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $this->oRedis->setex($sTokenKey, config('user.token_expire_time'), $iUserId);
        $this->oRedis->setex($sUserIdTokenKey, config('user.token_expire_time'), $sToken);
    }

    public function flushToken($iUserId)
    {
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $sToken          = $this->oRedis->get($sUserIdTokenKey);
        $sTokenKey       = $this->getRedisTokenKey($sToken);
        if ($sToken) {
            $this->oRedis->del($sTokenKey);
            $this->oRedis->del($sUserIdTokenKey);
        }
    }

    public function refreshToken($iUserId)
    {
        $oUser  = $this->oUserRepo->findById($iUserId);
        $sToken = $this->makeToken($oUser);
        $this->flushToken($iUserId);
        $this->setToken($sToken, $iUserId);
        return $sToken;
    }

    public function addTokenExpireTime($sToken)
    {
        $iUserId         = $this->getUserIdByToken($sToken);
        $sTokenKey       = $this->getRedisTokenKey($sToken);
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $this->oRedis->expire($sTokenKey, config('user.token_expire_time'));
        $this->oRedis->expire($sUserIdTokenKey, config('user.token_expire_time'));
    }

    public function addTokenExpireTimeByFd($iFd)
    {
        $iUserId = $this->getUserIdByFd($iFd);
        $sToken  = $this->getTokenByUserId($iUserId);
        $this->addTokenExpireTime($sToken);
    }

    public function logout($iUserId)
    {
        // 清token
        $this->flushToken($iUserId);
    }

}
