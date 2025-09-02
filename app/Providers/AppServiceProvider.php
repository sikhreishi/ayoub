<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use App\Services\Interfaces\IAuthService;
use App\Services\AuthService;
use App\Events\PaymentCompleted;
use Illuminate\Support\Facades\Event;
use App\Listeners\SendPaymentNotification;
class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);

        $this->app->singleton(\App\Services\GoogleMapsService::class, function ($app) {
            return new \App\Services\GoogleMapsService();
        });
        Event::listen(
            // PaymentCompleted::class,
            [SendPaymentNotification::class, 'handle']
        );
    }


    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
