<?php

if (function_exists('newrelic_record_datastore_segment')) {
    throw new RuntimeException(
        "`newrelic` extension must NOT be loaded during tests (`newrelic_record_datastore_segment` exists)."
    );
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/functions.php';
