<?php

namespace App\Jobs\Middleware;

use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\Facades\Redis;

class RateLimited
{
    /**
     * Process the queued job.
     *
     * @param  mixed    $job
     * @param  callable $next
     * @return mixed
     * @throws LimiterTimeoutException
     */
    public function handle($job, $next)
    {
        Redis::throttle('torn-api')
            ->block(0)->allow(10)->every(2)
            ->then(
                function () use ($job, $next) {
                    // Lock obtained...
                    $next($job);
                },
                function () use ($job) {
                    // Could not obtain lock...
                    $job->release(5);
                }
            );
    }
}
