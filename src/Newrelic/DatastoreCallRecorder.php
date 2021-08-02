<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelic\Newrelic;

use RuntimeException;
use Samuelnogueira\CacheDatastoreNewrelic\DatastoreParams;

use function function_exists;
use function newrelic_record_datastore_segment;

/**
 * @internal
 */
final class DatastoreCallRecorder
{
    /** @var DatastoreParams */
    private $params;

    /**
     * @throws RuntimeException If `newrelic` extension is not loaded.
     */
    public function __construct(DatastoreParams $params)
    {
        if (! function_exists('newrelic_record_datastore_segment')) {
            throw new RuntimeException(
                "`newrelic` extension must be loaded (missing `newrelic_record_datastore_segment` function)."
            );
        }

        $this->params = $params;
    }

    /**
     * @return false|mixed
     */
    public function record(callable $callable, string $operation)
    {
        return newrelic_record_datastore_segment($callable, $this->params->asSegmentParams($operation));
    }
}
