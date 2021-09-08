<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use App\Model\Config;

class InsertHlsDomainToConfig extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oConfig = new Config();
        $oConfig->name = 'hls_server_domain';
        $oConfig->value = 'http://192.168.64.104:1993/hls/';
        $oConfig->save();
    }
}
