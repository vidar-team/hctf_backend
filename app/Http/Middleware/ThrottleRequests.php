<?php

namespace App\Http\Middleware;

use APIReturn;
use Closure;
use Illuminate\Cache\RateLimiter;

class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    public function __construct(RateLimiter $limiter)
    {
        parent::__construct($limiter);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $maxAttempts
     * @param  float|int  $decayMinutes
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            return APIReturn::error("too_many_requests", __("操作过于频繁"), 429);
        }

        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }
}
