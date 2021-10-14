<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Validators\UserValidator;
use Hyperf\Di\Annotation\Inject;

class UserController extends AbstractController
{
    /**
     * @Inject
     * @var UserValidator
     */
    protected $oUserValidator;

    public function register()
    {
        $this->oUserValidator->userRegisterCheck($this->oRequest->all());
        $sUsername  = $this->oRequest->input('username');
        $sPasssword = $this->oRequest->input('password');
        $sToken     = $this->oUserService->register($sUsername, $sPasssword);
        return $this->success(['token' => $sToken]);
    }

    public function login()
    {
        $this->oUserValidator->userLoginCheck($this->oRequest->all());
        $sUsername  = $this->oRequest->input('username');
        $sPasssword = $this->oRequest->input('password');
        $sToken     = $this->oUserService->login($sUsername, $sPasssword);
        $this->oLogger->info($sUsername . ' login success!');
        return $this->success(['token' => $sToken]);
    }

    public function updateToken()
    {
        $iUserId = $this->getUserId();
        $sToken  = $this->oUserService->updateToken($iUserId);
        return $this->success(['token' => $sToken]);
    }

    public function logout()
    {
        $iUserId = $this->getUserId();
        $this->oUserService->logout($iUserId);
        return $this->success();
    }

    public function info()
    {
        return $this->success();
    }

}
