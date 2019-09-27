<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\NewsProviderInterface', 'App\NewsProvider');
        
        $this->app->bind('Psr\SimpleCache\CacheInterface', function () {
            return $this->app->make('Illuminate\Contracts\Cache\Repository');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
