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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'payment' => [
        'endpoint' => env('PAYMENT_ENDPOINT', 'https://api.micuentaweb.pe'),
        'username' => env('PAYMENT_USERNAME', '58901360'),
        'password' => env('PAYMENT_PASSWORD', 'testpassword_m5tRDPK4f3NWJPJ20Zz8G1GQB9TvElVRMtxC7yocxh7aC'),
        'publickey' => env('PAYMENT_PUBLICKEY', '58901360:testpublickey_HbQHAq4f5lzV5XKsCl49w564onu6Bm15DgK0B1KjfOdC7'),
        'hmac_secret' => env('PAYMENT_HMAC_SECRET', 'J6UzUcyFL7GSnKzZVdhnYgpOIIDdLXqVjFfSW2i52zyBJ'),
    ],

];
