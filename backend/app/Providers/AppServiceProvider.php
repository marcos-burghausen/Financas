<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Providers\Auth\AuthInterface;
use App\Providers\JWTAuthAdapter;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthInterface::class, JWTAuthAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceRootUrl(config('https://mrfinancas.burghausen.com.br'));
        }
        // Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
        //     $event->extendSocialite('facebook', \SocialiteProviders\Facebook\Provider::class);
        // });
    }
}
