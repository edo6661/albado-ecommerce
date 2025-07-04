<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id'=> env('MIDTRANS_MERCHANT_ID'), 
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false), 
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    // 'append_notif_url' => env('MIDTRANS_APPEND_NOTIF_URL'),
    // 'overwrite_notif_url' => env('MIDTRANS_OVERWRITE_NOTIF_URL'),
];