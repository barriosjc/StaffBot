<?php

return [
    'driver' => env('WHATSAPP_DRIVER', 'wwebjs'),

    'wwebjs' => [
        'base_url' => env('WHATSAPP_WWEBJS_URL', 'http://localhost:3000'),
        'api_key'  => env('WHATSAPP_WWEBJS_API_KEY', 'miclaveapi123'),
        'session'  => env('WHATSAPP_WWEBJS_SESSION', 'mysession'),
    ],

    'whatsapp_cloud' => [
        'phone_number_id' => env('WHATSAPP_CLOUD_PHONE_ID'),
        'token'           => env('WHATSAPP_CLOUD_TOKEN'),
    ],
];
