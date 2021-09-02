<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

class UserController extends AbstractController
{
	protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get();
    }

    public function login()
    {
    	return 123;
    }

}
