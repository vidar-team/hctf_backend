<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class VerifyJWTToken
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
        if (! $token = $this->auth->setRequest($request)->getToken()) {
//            return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            return $this->respond()->json(['token_not_provided'], 400);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
//            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
            return $this->respond()->json(['token_expired'], $e->getStatusCode());
        } catch (JWTException $e) {
//            return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
            return $this->respond()->json(['token_invalid'], $e->getStatusCode());
        }

        if (! $user) {
//            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            return $this->respond()->json(['user_not_found'], 404);
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
