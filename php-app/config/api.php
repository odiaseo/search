<?php

// API specific configuration values.
return array(
    "version" => array(
        // URI prefix (i.e.: api/v), to append major and minor versions. No leading or trailing /.
        "prefix" => "api/v",
        // Mandatory major version.
        'major' => 1,
        // Optional minor version. Set to null to disable it.
        'minor' => 4,
        // Optional patch version. Set to null to disable it.
        'patch' => 0,
    )
);
