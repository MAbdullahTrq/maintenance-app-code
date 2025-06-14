<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMTP2GO Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your SMTP2GO settings for sending emails.
    |
    */

    'api' => [
        'key' => env('SMTP2GO_API_KEY'),
        'endpoint' => 'https://api.smtp2go.com/v3',
        'timeout' => 30,
        'retry_attempts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sender Settings
    |--------------------------------------------------------------------------
    |
    | Configure the default sender settings.
    |
    */

    'sender' => [
        'email' => env('MAIL_FROM_ADDRESS', 'noreply@yourdomain.com'),
        'name' => env('MAIL_FROM_NAME', 'MaintainXtra'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Tracking Settings
    |--------------------------------------------------------------------------
    |
    | Configure email tracking and analytics settings.
    |
    */

    'tracking' => [
        'opens' => true,
        'clicks' => true,
        'unsubscribes' => true,
        'bounces' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for email events.
    |
    */

    'webhook' => [
        'enabled' => env('SMTP2GO_WEBHOOK_ENABLED', false),
        'url' => env('SMTP2GO_WEBHOOK_URL'),
        'secret' => env('SMTP2GO_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting settings.
    |
    */

    'rate_limit' => [
        'enabled' => env('SMTP2GO_RATE_LIMIT_ENABLED', false),
        'requests' => env('SMTP2GO_RATE_LIMIT_REQUESTS', 100),
        'period' => env('SMTP2GO_RATE_LIMIT_PERIOD', 60), // in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    |
    | Configure general email settings.
    |
    */

    'email' => [
        'max_size' => 50 * 1024 * 1024, // 50MB in bytes
        'max_recipients' => 100,
        'default_charset' => 'UTF-8',
    ],
]; 