<?php
/**
 * Created by PhpStorm.
 * User: WangSF
 * Date: 2017/12/20 0020
 * Time: 17:46
 */

namespace GeXingW\QingStorStorage;


use QingStor\SDK\Config;

class QingStorConfig extends Config
{
    public function __construct(string $access_key_id = '', string $secret_access_key = '')
    {
        parent::__construct($access_key_id, $secret_access_key);
    }

}