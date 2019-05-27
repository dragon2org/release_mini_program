<?php


namespace App\Facades;

/**
 *
 * @see \App\Services\ReleaseService
 */
use Illuminate\Support\Facades\Facade;

class ReleaseFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return \App\Services\ReleaseService
     */
    protected static function getFacadeAccessor()
    {
        return 'dhb.component.core';
    }

    /**
     *
     * @return \App\Services\ReleaseService
     */
    public static function service()
    {
        return app('dhb.component.core');
    }
}