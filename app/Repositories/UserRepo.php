<?php
declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;
use App\Model\User;
use App\Exception\ExceptionCode as ExCode;

class UserRepo extends BaseRepo
{

	/**
	 * @Inject
	 * @var User
	 */
	protected $oUser;

	public function findByUsername($sUsername)
	{
		return $this->oUser->where('username', $sUsername)->first();
	}

	public function create($sUsername, $sPassword): User
	{
		$oUser = new $this->oUser();
		$oUser->username = $sUsername;
		$oUser->password = $sPassword;
		$bDbResult = $oUser->save();
		if (! $bDbResult) {
            ExCode::fire(ExCode::USER_CREATE_ERROR);
		}
		return $oUser;
	}

}
