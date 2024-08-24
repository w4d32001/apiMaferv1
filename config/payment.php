<?php

return [
    'endpoint' => env('PAYMENT_ENDPOINT', 'https://api.micuentaweb.pe'),
    'username' => env('PAYMENT_USERNAME'),
    'password' => env('PAYMENT_PASSWORD'),
    'publickey' => env('PAYMENT_PUBLICKEY'),
    'hmac_secret' => env('PAYMENT_HMAC_SECRET'),
];