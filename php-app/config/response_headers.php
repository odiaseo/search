<?php
return [

    /*
    |---------------------------------------------------------------------------
    | Response Header Prefix
    |---------------------------------------------------------------------------
    |
    | Here you may specify a prefix for all the response headers. If NULL,
    | it won't be added by the ResponseHeadersMiddleware.
    |
    */

    'prefix' => 'X-QPlatform-',

    /*
    |---------------------------------------------------------------------------
    | Response Headers
    |---------------------------------------------------------------------------
    |
    | Add to this array the headers you want to be added to any API response.
    | The headers are set by the ResponseHeadersMiddleware, which needs to
    | be registered as a global middleware in Http/Kernel.php. If the
    | header's value is NULL, it won't be added to the response.
    |
    */

    'headers' => [
        'Pod-ID'           => env('POD_ID'),
        'Build-No'         => env('BUILD_NO'),
        'Build-Date'       => env('BUILD_DATE'),
        'Release-Datetime' => env('RELEASE_DATETIME'),
        'Commit-Hash'      => env('COMMIT_HASH'),
    ]
];
