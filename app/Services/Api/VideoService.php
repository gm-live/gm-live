<?php
declare (strict_types = 1);

namespace App\Services\Api;

use Hyperf\Di\Annotation\Inject;
use Exception;
use App\Repositories\ConfigRepo;
use App\Exception\ExceptionCode as ExCode;

class VideoService extends BaseApiService
{

	/**
	 * @Inject
	 * @var ConfigRepo
	 */
	protected $oConfigRepo;

    public function getVideoUrlByRoomId($iRoomId)
    {
        $sHlsServerDomain = $this->oConfigRepo->getValueByName('hls_server_domain');

        // TODO 房間影片配對機制
        $sVideoName = '123.m3u8';
        $sVideoUrl = $sHlsServerDomain . $sVideoName;

        return $sVideoUrl;
    }
}
