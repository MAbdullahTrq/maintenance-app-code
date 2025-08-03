<?php

namespace App\Providers;

use App\Services\SmsService;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client as TwilioClient;

class TwilioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TwilioClient::class, function ($app) {
            return new TwilioClient(
                config('twilio.account_sid'),
                config('twilio.auth_token')
            );
        });

        $this->app->singleton(SmsService::class, function ($app) {
            return new SmsService($app->make(TwilioClient::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 