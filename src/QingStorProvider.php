<?php

namespace GeXingW\QingStorStorage;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

/**
 * Created by PhpStorm.
 * User: WangSF
 * Date: 2017/12/18 0018
 * Time: 16:34
 */
class QingStorProvider extends ServiceProvider
{
    public function boot()
    {
        \Storage::extend('QingStor', function ($app, $config) {
            $client = QingStorClient::getInstance($config);

            $bucket = array_get($config, 'bucket');
            $zone = array_get($config, 'zone');

            $adapter = new QingStorStorage($client, $bucket, $zone);
            return new Filesystem($adapter);
        });
    }

    public function register()
    {

    }

}