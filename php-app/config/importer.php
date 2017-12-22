<?php

use MapleSyrupGroup\Search\Services\IndexStatusTracker\TrackerFactory;

return [
    'lock_file_location' => env('IMPORTER_LOCK_FILE_LOCATION', sys_get_temp_dir()),
    'lock_file_ttl'      => env('IMPORTER_LOCK_FILE_TTL', 300),
    'tracker_alias'      => env('IMPORTER_TRACKER_ALIAS', TrackerFactory::REDIS),
];
