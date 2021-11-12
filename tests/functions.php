<?php

use Samuelnogueira\CacheDatastoreNewrelicTests\Stubs\NewrelicMock;

/**
 * @param array<string, string> $params
 * @return false|mixed
 */
function newrelic_record_datastore_segment(callable $callable, array $params)
{
    return NewrelicMock::recordDatastoreSegment($callable, $params);
}
