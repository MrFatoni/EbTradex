<?php

return [
    'private_key' => env('COINPAYMENT_PRIVATE_KEY', ''),
    'public_key' => env('COINPAYMENT_PUBLIC_KEY', ''),
    'merchant_id' => env('COINPAYMENT_MERCHANT_ID', ''),
    'ipn_secret' => env('COINPAYMENT_IPN_SECRET', ''),
    'ipn_url' => env('COINPAYMENT_IPN_URL', ''),
    'ch' => env('COINPAYMENT_CH', null),
];