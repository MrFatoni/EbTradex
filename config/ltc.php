<?php

return [
    'scheme' => env('LTC_API_SCHEME', 'http'),
    'host' => env('LTC_API_HOST', 'localhost'),
    'port' => env('LTC_API_PORT', 8332),
    'user' => env('LTC_API_RPCUSER'),
    'password' => env('LTC_API_RPCPASSWORD'),
    'ca' => env('LTC_API_SSL_CERT'),
];