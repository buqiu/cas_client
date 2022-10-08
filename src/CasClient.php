<?php

namespace Buqiu\CasClient;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CasClient
{
    protected $config;

    protected $_attributes = [];

    protected $sessionId;

    protected $uri;

    private $port;


    public function __construct(array $config)
    {
        $this->parseConfig($config);
        $this->sessionId = request()->session()->getId();
        $this->port = 'http://';
        if ($this->config['cas_port'] == '443') {
            $this->port = 'https://';
        }

    }

    protected function parseConfig(array $config)
    {
        $defaults = [
            'cas_hostname' => '',
            'cas_session_name' => 'CASAuth',
            'cas_session_lifetime' => 7200,
            'cas_session_path' => '/',
            'cas_control_session' => false,
            'cas_session_httponly' => true,
            'cas_port' => 443,
            'cas_uri' => '/cas',
            'cas_validation' => '',
            'cas_cert' => '',
            'cas_proxy' => false,
            'cas_validate_cn' => true,
            'cas_login_url' => '',
            'cas_logout_url' => '',
            'cas_logout_redirect' => '',
            'cas_redirect_path' => '',
            'cas_enable_saml' => true,
            'cas_version' => "2.0",
            'cas_debug' => false,
            'cas_verbose_errors' => false,
            'cas_masquerade' => '',
        ];
        $this->config = array_merge($defaults, $config);
    }

    /**
     * Notes: 判断是否登录
     * User : smallK
     * Date : 2022/1/17
     * Time : 1:47 下午
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function checkAuthentication()
    {
        $ticket = request()->get('ticket', '');

        if ($ticket != '' && ! request()->session()->get('cas_login_status')) {

            $url = $this->port.$this->config['cas_hostname'].$this->config['cas_uri'].'/serviceValidate?';
            $query = [
                'service' => urlencode(request()->url()),
                'format' => 'json',
                'session_id' => request()->session()->getId(),
                'ticket' => $ticket,
            ];
            $result = Http::get($url.http_build_query($query));

            if ($result && isset($result['serviceResponse']['authenticationSuccess'])) {
                $this->setSession($result);
            }

            return true;
        } else {

            $url = $this->port.$this->config['cas_hostname'].$this->config['cas_uri'].'/checkAuth?';

            $query = [
                'service' => urlencode(request()->url()),
                'format' => 'json',
                'session_id' => request()->session()->getId(),
                'token' => request()->session()->get('cas_token'),
            ];

            $result = Http::get($url.http_build_query($query));

            if ($result && isset($result['serviceResponse']['authenticationSuccess'])) {

                if ($result['serviceResponse']['authenticationSuccess']['authentication'] && request()->session()->get('cas_login_status')) {

                    $this->setSession($result);

                    return true;
                } else {
                    $this->rmSession();

                    return false;
                }
            } else {
                $this->rmSession();

                return false;
            }
        }

    }


    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 1:47 下午
     * @return mixed
     */
    public function user()
    {
        if (request()->session()->get('cas_login_status')) {
            return request()->session()->get('cas_user');
        }
    }

    public function authenticate()
    {
        $url = $this->port.$this->config['cas_hostname'].$this->config['cas_uri'].'/login?';

        $query = ['service' => urlencode(request()->url())];

        $serviceUrl = $url.http_build_query($query);

        return redirect($serviceUrl);
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 1:47 下午
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAttributes()
    {
        if (request()->session()->get('cas_login_status')) {
            return session()->get('cas_attributes');
        }
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/17
     * Time : 11:16 上午
     * @param $result
     */
    public function setSession($result)
    {
        request()->session()->put('cas_login_status', true);
        request()->session()->put('cas_user', $result['serviceResponse']['authenticationSuccess']['user']);
        request()->session()->put('cas_attributes', $result['serviceResponse']['authenticationSuccess']['attributes']);
        if (isset($result['serviceResponse']['authenticationSuccess']['token'])) {
            request()->session()->put('cas_token', $result['serviceResponse']['authenticationSuccess']['token']);
        }
    }

    public function rmSession()
    {
        request()->session()->remove('cas_login_status');
        request()->session()->remove('cas_user');
        request()->session()->remove('cas_attributes');
        request()->session()->remove('cas_token');

    }

}
