<?php

namespace App\Providers;

use App\Component\Component;
use App\Component\MiniProgram;
use Composer\Console\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('sjje.open_platform.component', function($app){
            return new Component($app);
        });

        $this->app->singleton('sjje.open_platform.mini_program', function($app){
            return new MiniProgram($app);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
