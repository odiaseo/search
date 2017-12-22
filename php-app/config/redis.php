<?php

return [
    'scheme'   => env('REDIS_SCHEME', 'tcp'),
    'host'     => env('REDIS_HOST', '127.0.0.1'),
    'port'     => env('REDIS_PORT', 6379),
    'database' => env('REDIS_DATABASE', 4),
    'prefix'   => env('REDIS_PREFIX', ''),
];
