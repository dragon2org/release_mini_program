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
            /*
             * Driver name
             */
            'driver' => 'kafka',

            /*
             * The name of default queue.
             */
            'queue' => env('KAFKA_QUEUE', 'default'),

            /*
             * The group of where the consumer in resides.
             */
            'consumer_group_id' => env('KAFKA_CONSUMER_GROUP_ID', 'laravel_queue'),

            /*
             * Address of the Kafka broker
             */
            'brokers' => env('KAFKA_BROKERS', 'localhost'),

            /*
             * Determine the number of seconds to sleep if there's an error communicating with kafka
             * If set to false, it'll throw an exception rather than doing the sleep for X seconds.
             */
            'sleep_on_error' => env('KAFKA_ERROR_SLEEP', 5),

            /*
             * Sleep when a deadlock is detected
             */
            'sleep_on_deadlock' => env('KAFKA_DEADLOCK_SLEEP', 2),

            /*
             * sasl authorization
             */
            'sasl_enable' => env('KAFKA_SASL_ENABLE', true),

            /*
             * File or directory path to CA certificate(s) for verifying the broker's key. example: storage_path('kafka.client.truststore.jks')
             */
            'ssl_ca_location' => storage_path('kafka.client.truststore.jks'),

            /*
             * SASL username for use with the PLAIN and SASL-SCRAM-.. mechanisms
             */
            'sasl_plain_username' => env('KAFKA_SASL_PLAIN_USERNAME'),

            /*
             * SASL password for use with the PLAIN and SASL-SCRAM-.. mechanism
             */
            'sasl_plain_password' => env('KAFKA_SASL_PLAIN_PASSWORD'),
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
