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

    public function buildResponse($key, $maxAttempts)
    {
        $response = APIReturn::error('too_many_requests', '操作过于频繁', 429);

        $retryAfter = $this->limiter->availableIn($key);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
    }
}
