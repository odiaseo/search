<?php

return array(
    "syslog" => array(
        "socket"      => env('PAPERTRAIL_SOCKET', false),
        "system-name" => env('PAPERTRAIL_SYSTEM_NAME', false),
    )
);
