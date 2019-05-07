<?php


namespace App\Logs;


use App\CustomLogger;
use App\Models\MiniProgram;

class ReleaseAuditLog
{
    public static function info($message, $data)
    {
        //$message = "miniProgram: {$miniProgram->app_id}; ".$message;

        CustomLogger::info(CustomLogger::LOG_AUDIT_NOTICE, $message, $data);
    }
}