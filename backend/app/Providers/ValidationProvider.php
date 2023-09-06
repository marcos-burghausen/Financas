<?php

namespace App\Providers;

use App\Security\Validation\DevValidation;
use Illuminate\Support\ServiceProvider;

class ValidationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('DevValidation', fn () => new DevValidation);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
