<?php
/**
 * Created by PhpStorm.
 * User: harlen
 * Date: 2019/4/27
 * Time: 10:30 PM
 */

namespace App\Logs;


use App\CustomLogger;
use App\Models\MiniProgram;

class ReleaseQueueFailureLog
{
    public static function info(MiniProgram $miniProgram, $config, $templateId, $action, $version)
    {
        $message = "小程序 {$miniProgram->app_id} 入列成功; TemplateId: {$templateId}; 操作: {$action}; script版本: " . $version;

        CustomLogger::info(CustomLogger::LOG_IN_QUEUE, $message, $config);
    }
}