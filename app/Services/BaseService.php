<?php
declare (strict_types = 1);

namespace App\Services;

use App\Exception\ExceptionCode as ExCode;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Server\ServerFactory;

class BaseService
{

    /**
     * @Inject
     * @var Redis
     */
    protected $oRedis;

    public function getContainer()
    {
        return ApplicationContext::getContainer();
    }

    public function getServer()
    {
        return $this->getContainer()->get(ServerFactory::class)->getServer()->getServer();
    }

	public function getRedisTokenKey($sToken)
    {
        return sprintf(config('user.token_key'), $sToken);
    }

    public function getRedisUserIdTokenKey($iUserId)
    {
        return sprintf(config('user.user_id_token_key'), $iUserId);
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

    public function getFdUserMapKey()
    {
        return config('chatRoom.fd_user_id_map_key');
    }

    public function getUserIdByFd($iFd)
    {
        $sFdKey = $this->getFdUserMapKey();
        return $this->oRedis->hget($sFdKey, (string)$iFd);
    }

    public function getTokenByUserId($iUserId)
    {
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        return $this->oRedis->get($sUserIdTokenKey);
    }
}
