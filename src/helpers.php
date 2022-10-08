<?php

namespace Buqiu\CasClient;

use Illuminate\Contracts\Foundation\Application;

if ( ! function_exists('cas')) {
    /**
     * Notes: cas
     * User : smallK
     * Date : 2022/1/14
     * Time : 11:33 上午
     * @return Application|mixed
     */
    function cas()
    {
        return app('cas');
    }
}
