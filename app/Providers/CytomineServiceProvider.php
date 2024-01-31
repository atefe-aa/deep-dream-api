<?php

namespace App\Providers;

use App\Services\CytomineAuthService;
use App\Services\CytomineProjectService;
use Illuminate\Support\ServiceProvider;

class CytomineServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CytomineAuthService::class, function ($app) {
            return new CytomineAuthService();
        });

        $this->app->bind(CytomineProjectService::class, function ($app) {
            return new CytomineProjectService($app->make(CytomineAuthService::class));
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
