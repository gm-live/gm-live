<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Hyperf\Logger\LoggerFactory;
use App\Validators\VideoValidator;
use App\Services\Api\VideoService;
use Hyperf\Di\Annotation\Inject;

class VideoController extends AbstractController
{

	/**
	 * @Inject
	 * @var VideoValidator
	 */
	protected $oVideoValidator;

    /**
     * @Inject
     * @var VideoService
     */
    protected $oVideoService;

	public function getVideo()
    {
    	$this->oVideoValidator->getVideoOriginCheck($this->oRequest->all());
    	$iRoomId = $this->oRequest->input('room_id');
    	$sVideoUrl = $this->oVideoService->getVideoUrlByRoomId($iRoomId);
   		return $this->success(['video_url' => $sVideoUrl]);
    }

}
