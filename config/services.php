<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'msg91' => [
        'auth_key' => env('MSG91_AUTH_KEY'),
        'sender_id' => env('MSG91_SENDER_ID'),
        'templates' => [
            'login' => env('MSG91_TEMPLATE_LOGIN'),
            'registration' => env('MSG91_TEMPLATE_REGISTRATION'),
        ],
        'otp_length' => env('MSG91_OTP_LENGTH', 6),
        'otp_expiry' => env('MSG91_OTP_EXPIRY', 10),
        'country_code' => env('MSG91_COUNTRY_CODE', '91'),
        'test_phone' => env('MSG91_TEST_PHONE'),
        'test_otp' => env('MSG91_TEST_OTP'),
    ],

    'otp' => [
        'driver' => env('OTP_DRIVER', 'log'),
    ],

    'horoscope' => [
        'url' => env('HOROSCOPE_SERVICE_URL', 'http://localhost:8100'),
    ],

    'switchpay' => [
        'token' => env('SWITCHPAY_TOKEN'),
        'user_uuid' => env('SWITCHPAY_UUID'),
        'base_url' => env('SWITCHPAY_BASE_URL', 'https://www.switchpay.in'),
        'payment_modes' => env('SWITCHPAY_PAYMENT_MODES', 'cc|dc|upi|netbanking'),
    ],

];
