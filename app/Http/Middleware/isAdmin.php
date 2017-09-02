<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Closure;

/**
 * Class isAdmin
 * 判断是否为管理员权限
 * 调用本中间件之前需要判定token有效
 * @package App\Http\Middleware
 * @author Eridanus Sora <sora@sound.moe>
 */
class isAdmin extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $this->auth->setRequest($request)->getToken();
        $user = $this->auth->authenticate($token);
        if ($user->admin){
            return $next($request);
        }
        return \APIReturn::error("permission_denied", "本操作需要管理员权限", 403);
    }
}
