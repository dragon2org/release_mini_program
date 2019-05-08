<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'kafka' => [
            'driver' => 'kafka',
            'sasl'=> false,
            'sasl_plain_username' => env('KAFKA_SASL_PLAIN_USERNAME', 'YOUR AK'), // 阿里云 ak
            'sasl_plain_password' => env('KAFKA_SASL_PLAIN_PASSWORD', 'YOUR AC'),// 阿里云 ac后10位
            'bootstrap_servers' => env('KAFKA_BROKER_LIST'), // broker
            'ssl.ca.location' => storage_path('config/ca-cert'), // cr 证书 下载 https://help.aliyun.com/document_detail/52376.html
            'message.send.max.retries' => 5,
            'queue' => env('KAFKA_TOPIC', 'release-mini-program'),  // 这里填入你在阿里云控制台配置的topic
            'consumer_id' => env('KAFKA_CONSUMER_ID', '1'), // 消费者ID，你在阿里云控制台配置的消费之ID
            'log_level' => LOG_DEBUG, // 日志等级
            'security.protocol' => 'plaintext',  //plaintext, ssl, sasl_plaintext, sasl_ssl
            'max.tries' => '5', // 最大尝试次数
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
