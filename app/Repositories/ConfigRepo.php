<?php
declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;
use App\Model\Config;

class ConfigRepo extends BaseRepo
{

	/**
	 * @Inject
	 * @var Config
	 */
	protected $oConfig;

	public function getValueByName($sName, $sDefaultValue = '')
	{
		return $this->oConfig->where('name', $sName)->first()->value ?? $sDefaultValue;
	}

}
