<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FirebaseServiceBase;
use App\Services\FirebaseServiceBaseInterface;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseServiceBaseInterface::class, FirebaseServiceBase::class);
                 $this->app->singleton('firebase_service', function ($app) {
                    return $app->make(FirebaseServiceBaseInterface::class);
                });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
