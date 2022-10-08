<?php

namespace Buqiu\CasClient\Providers;

use Buqiu\CasClient\CasClient;
use Illuminate\Support\ServiceProvider;

class CasClientProvider extends ServiceProvider
{
    /**
     * Notes: 注册应用程序服务。
     * User : smallK
     * Date : 2022/1/14
     * Time : 11:26 上午
     */
    public function register()
    {
        $this->app->singleton('cas', function () {
            return new CasClient(config('cas_client'));
        });
    }

    /**
     * Notes: 发布配置文件
     * User : smallK
     * Date : 2022/1/14
     * Time : 11:28 上午
     */
    public function boot()
    {
        $configPath = __DIR__.'/../../config/config.php';

        $this->publishes(
            [
                $configPath => config_path('cas_client.php'),
            ],
            'buqiu-config-client-cas'
        );

    }
}
