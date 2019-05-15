<?php
/**
 * Created by PhpStorm.
 * User: 赵思贵
 * Date: 2018/9/4
 * Time: 9:10
 */
namespace App;


/**
 * 自定义日志
 *
 * Class CustomLogger
 * @package App\Helper
 *
 * ```php
 * \App\Helper\CustomLogger::getLogger(\App\Helper\CustomLogger::LOG_QYWX)->error("123");
 * \App\Helper\CustomLogger::getLogger(\App\Helper\CustomLogger::LOG_QYWX)->info("info");
 * ```
 */
class CustomLogger
{
    const LOG_ERROR = 'error';

    const LOG_IN_QUEUE = 'queue-in';

    const LOG_OUT_QUEUE = 'queue-out';

    const LOG_COMMON_QUEUE = 'queue-common';

    const LOG_AUDIT_NOTICE = 'audit-notice';

    const LOG_CODE_COMMIT = 'code-commit';

    const LOG_MINI_PROGRAM_AUTHORIZATION_LOG = 'mini-program-authorization';


    private static $loggers = array();

    /**
     * 获取log实例
     *
     * @param string $type
     * @param int $day
     * @return \Illuminate\Log\Writer
     */
    public static function getLogger($type = self::LOG_ERROR, $day = 30)
    {
        if (empty(self::$loggers[$type])) {
            self::$loggers[$type] = new \Illuminate\Log\Writer(new \Monolog\Logger($type));
            $file = env('CUSTOM_LOGGER_DIR') . '/'. env('APP_NAME') .'-' . $type . '.log';
            $log = self::$loggers[$type];
            $log->useDailyFiles($file, $day);
        }
        return self::$loggers[$type];
    }

    public static function emergency($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->emergency($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function alert($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->alert($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function critical($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->critical($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function error($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->error($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function warning($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->warning($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function notice($type, string $message,array $data = [])
    {
        try {
            (self::getLogger($type))->notice($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function info($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->info($message, $data);
        } catch (\Exception $e) {
        }
    }

    public static function debug($type, string $message, array $data = [])
    {
        try {
            (self::getLogger($type))->debug($message, $data);
        } catch (\Exception $e) {
        }
    }
}