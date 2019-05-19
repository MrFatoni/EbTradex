<?php

return [
    'scheme' => env('BTC_API_SCHEME', 'http'),
    'host' => env('BTC_API_HOST', 'localhost'),
    'port' => env('BTC_API_PORT', 8332),
    'user' => env('BTC_API_RPCUSER'),
    'password' => env('BTC_API_RPCPASSWORD'),
    'network_fee' => env('BTC_API_NETWORK_FEE', 0.00001),
    'ca' => env('BTC_API_SSL_CERT'),
];