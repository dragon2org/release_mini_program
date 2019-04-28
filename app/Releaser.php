<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/27
 * Time: 10:58 PM
 */

namespace App;


use App\Models\Component;
use App\Services\ReleaseService;

class Releaser
{
    static $component = [];

    /**
     * @param $componentAppId
     * @return ReleaseService|mixed
     * @throws Exceptions\UnprocessableEntityHttpException
     */
    public static function build($componentAppId)
    {
        if(empty(self::$component[$componentAppId])){
            $component = (new Component())->getComponent($componentAppId);
            $service = (new ReleaseService($component));
            $service->setOpenPlatform();
            return self::$component[$componentAppId] = $service;
        }
        return self::$component[$componentAppId];
    }

    public static function flush()
    {
        self::$component = [];
    }
}