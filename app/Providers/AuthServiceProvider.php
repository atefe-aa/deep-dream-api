<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Counsellor;
use App\Models\Price;
use App\Models\Test;
use App\Models\TestType;
use App\Policies\CounsellorPolicy;
use App\Policies\PricePolicy;
use App\Policies\TestPolicy;
use App\Policies\TestTypePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Test::class => TestPolicy::class,
        Counsellor::class => CounsellorPolicy::class,
        TestType::class => TestTypePolicy::class,
        Price::class => PricePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
