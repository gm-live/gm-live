<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Services\Api\UserService;
use Hyperf\Logger\LoggerFactory;
use App\Validators\UserValidator;
use Hyperf\Di\Annotation\Inject;

class UserController extends AbstractController
{

	/**
	 * @Inject
	 * @var UserValidator
	 */
	protected $oUserValidator;

	/**
	 * @Inject
	 * @var UserService
	 */
	protected $oUserService;

	public function register()
    {
    	$this->oUserValidator->userRegisterCheck($this->oRequest->all());
    	$sUsername = $this->oRequest->input('username');
    	$sPasssword = $this->oRequest->input('password');
    	$sToken = $this->oUserService->register($sUsername, $sPasssword);
   		return $this->success(['token' => $sToken]);
    }

    public function login()
    {
    	$this->oUserValidator->userLoginCheck($this->oRequest->all());
    	$sUsername = $this->oRequest->input('username');
    	$sPasssword = $this->oRequest->input('password');
    	$sToken = $this->oUserService->login($sUsername, $sPasssword);
   		return $this->success(['token' => $sToken]);
    }

    public function info()
    {
        return $this->success();
    }

}
