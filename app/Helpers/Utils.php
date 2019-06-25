<?php


namespace App\Helpers;


class Utils
{
    public static function pageSize()
    {
        if(request()->input('pageSize')){
            return request()->input('pageSize');
        }

        return env('DEFAULT_PAGE_SIZE', 5);
    }
}