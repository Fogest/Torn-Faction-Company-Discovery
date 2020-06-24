<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(
            function (IncomingEntry $entry) {
                return true;

//                return $entry->isReportableException() ||
//                   $entry->isFailedRequest() ||
//                   $entry->isFailedJob() ||
//                   $entry->isScheduledTask() ||
//                   $entry->hasMonitoredTag();
            }
        );
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders(
            [
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            ]
        );
    }

    /**
     * Overload authorization method from \Laravel\Telescope\TelescopeApplicationServiceProvider
     * to allow access to Horizon without having a logged in user.
     *
     * @return void
     */
    protected function authorization()
    {
        Telescope::auth(
            function () {
                return true;
            }
        );
    }
}
