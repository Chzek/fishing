<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => Fishinglog\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'nas' => [
        'url' => env('NAS_URL', ''),
        'token' => env('NAS_API_TOKEN', ''),
        'target_name' => env('SYNC_TARGET_NAME', (env('APP_ENV') === 'production' ? 'Laptop' : 'NAS')),
        'instance_name' => env('SYNC_INSTANCE_NAME', (env('APP_ENV') === 'production' ? 'Synology NAS' : 'Field Laptop')),
    ],

];
