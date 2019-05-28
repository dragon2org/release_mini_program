<?php

namespace App\Providers;

use App\Models\ReleaseItem;
use App\Observers\ReleaseItemObserver;
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
        ReleaseItem::observe(ReleaseItemObserver::class);
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
