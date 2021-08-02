<?php

use Samuelnogueira\CacheDatastoreNewrelicTests\Stubs\NewrelicStub;

/**
 * @param array<string, string> $params
 * @return false|mixed
 */
function newrelic_record_datastore_segment(callable $callable, array $params)
{
    return NewrelicStub::recordDatastoreSegment($callable, $params);
}
