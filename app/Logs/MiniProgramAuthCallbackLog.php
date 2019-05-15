<?php


namespace App\Logs;


use App\CustomLogger;
use App\Models\Component;

class MiniProgramAuthCallbackLog
{
    public static function info(Component $component,  $data)
    {
        $message = "授权回调: 平台:{$component->app_id} :";
        CustomLogger::info(CustomLogger::LOG_MINI_PROGRAM_AUTHORIZATION_LOG, $message, $data);
    }
}