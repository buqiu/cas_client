<?php

namespace Buqiu\CasClient\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CasAuth
 * @package Buqiu\Cas\Middleware
 */
class CasAuth
{
    /**
     * @var Guard
     */
    protected $auth;
    /**
     * @var Application|mixed
     */
    protected $cas;

    /**
     * CasAuth constructor.
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->cas = app('cas');
    }

    /**
     * Notes:  Handle an incoming request.
     * User : smallK
     * Date : 2022/1/14
     * Time : 3:53 下午
     * @param  Request  $request
     * @param  Closure  $next
     * @return Application|ResponseFactory|Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if ($this->cas->checkAuthentication()) {

        } else {
            if ($request->ajax() && $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return $this->cas->authenticate();
        }

        return $next($request);
    }
}
