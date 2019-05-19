<?php
return [
    'client_id' => env('PAYPAL_CLIENT_ID', 'Ad4nGDX9R0ivxsGawr22MAAKBl5gfFA_tdFz0w509onhS5KinwPWKvNq4syXbuMTmRz0q7zxzPwdFUVU'),
    'secret' => env('PAYPAL_SECRET', 'EBmRFRe78b_R9dzpYK7lKk8-hUH7DdxligczII-v0rhPlIR-0jEyPfHVk30Y4ugdKNesGcq5KkwZlyNB'),
    'intent' => env('PAYPAL_INTENT', 'sale'),
    'return_url' => env('RETURN_URL', ''),
    'cancel_url' => env('CANCEL_URL', ''),
    'settings' => array(
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
];