<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Closure;

class BlockCheck extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $this->auth->setRequest($request)->getToken();
        $user = $this->auth->authenticate($token);
        if (!$user->banned) {
            return $next($request);
        }
        return \APIReturn::error("banned", "您已经被封禁", 403);
    }
}
