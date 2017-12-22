<?php

// Quidco specific configuration values.
return [
    "api" => [
        'endpoint' => env('QUIDCO_API_ENDPOINT', 'http://trunk.quidco.dev/api/v3/en/'),
        'authentication' => [
            'service_key' => env('QUIDCO_API_SERVICE_KEY', null),
            'client_id' => env('QUIDCO_API_CLIENT_ID', null)
        ]
    ]
];
