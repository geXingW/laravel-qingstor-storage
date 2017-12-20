<?php
/**
 * Created by PhpStorm.
 * User: WangSF
 * Date: 2017/12/20 0020
 * Time: 17:31
 */

namespace GeXingW\QingStorStorage;

use QingStor\SDK\Config;
use QingStor\SDK\Service\QingStor;

class QingStorClient
{
    public $service;

    public function __construct(array $config)
    {
        $accessId = array_get($config, 'access_key_id', null);
        $accessKey = array_get($config, 'secret_access_key', null);
        $qingstorConfig = new Config($accessId, $accessKey);
        $this->service = new QingStor($qingstorConfig);
    }

    public static function getInstance(array $config)
    {
        return new static($config);
    }
}