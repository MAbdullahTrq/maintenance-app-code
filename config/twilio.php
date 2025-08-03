<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Twilio SMS services.
    |
    */

    'account_sid' => env('TWILIO_ACCOUNT_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'from_number' => env('TWILIO_FROM_NUMBER'),
    
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS sending functionality.
    |
    */
    
    'sms' => [
        'enabled' => env('TWILIO_SMS_ENABLED', false),
        'from_number' => env('TWILIO_FROM_NUMBER'),
        'webhook_url' => env('TWILIO_WEBHOOK_URL'),
    ],
]; 