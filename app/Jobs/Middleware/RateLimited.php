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
            ->allow(75)->every(60)
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
