<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app('db')->listen(function ($query) {
            app('log')->channel('sql')->info(json_encode([
                'query' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time . ' ms'
            ], JSON_PRETTY_PRINT));
        });
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UserManager', function () {
            return new \App\Services\User\UserManager;
        });
        $this->app->singleton('ValidatorManager', function () {
            return new \App\Services\Validator\ValidatorManager;
        });
    }
}
