<?php


namespace App\Logs;


use App\CustomLogger;
use App\Models\MiniProgram;

class IllegalRequestLog
{
    public static function info($ip)
    {
        $message = "非法来源ip拒绝访问 : {$ip}; ";
        CustomLogger::info(CustomLogger::LOG_ILLEGAL_REQUEST, $message, []);
    }
}