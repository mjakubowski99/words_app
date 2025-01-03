<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Telescope\Telescope;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    protected function authorization(): void
    {
        $this->gate();

        Telescope::auth(function ($request) {
            return app()->environment('local')
                || $request->user('admin');
        });
    }

    public function register(): void
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if (config('app.debug')) {
                return true;
            }

            return $entry->isReportableException()
               || $entry->isFailedRequest()
               || $entry->isFailedJob()
               || $entry->isScheduledTask()
               || $entry->hasMonitoredTag();
        });
    }

    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }
}
