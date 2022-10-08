<?php

namespace Buqiu\CasClient\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

/**
 * Class RedirectCasAuthenticated
 * @package Buqiu\Cas\Middleware¬
 */
class RedirectCasAuthenticated
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
     * RedirectCasAuthenticated constructor.
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->cas = app('cas');
    }

    /**
     * Notes:
     * User : smallK
     * Date : 2022/1/14
     * Time : 3:57 下午
     * @param  Request  $request
     * @param  Closure  $next
     * @return Application|RedirectResponse|Redirector|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->cas->checkAuthentication()) {
            return redirect(config('cas_client.cas_redirect_path'));
        }

        return $next($request);
    }
}
