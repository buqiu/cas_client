<?php

namespace Buqiu\CasClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Cas
 * @package Buqiu\CasClient\Facades
 */
class Cas extends Facade
{
    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/14
     * Time : 11:34 上午
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cas';
    }
}
