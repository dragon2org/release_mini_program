<?php


namespace App\Logs;


use App\CustomLogger;
use App\Models\MiniProgram;

class CodeCommitLog
{
    public static function info(MiniProgram $miniProgram, $message, $data)
    {
        $message = "miniProgram: {$miniProgram->app_id}; ". $message;

        CustomLogger::info(CustomLogger::LOG_CODE_COMMIT, $message, $data);
    }
}