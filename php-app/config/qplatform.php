<?php

// Qplatform specific configuration values.
return [
    "api" => [
        'endpoints' => [
            'content_get_merchant_details' => env(
                'QPLATFORM_API_CONTENT_GET_MERCHANT_DETAILS',
                'http://content.app/api/v1.0/merchants/?includes=images,live_deals,categories,best_rates,related_merchant_rates,statistics'
            ),
        ]
    ]
];
