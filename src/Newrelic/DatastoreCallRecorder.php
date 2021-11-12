<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelic\Newrelic;

use RuntimeException;
use Samuelnogueira\CacheDatastoreNewrelic\DatastoreParams;
use Samuelnogueira\CacheDatastoreNewrelic\Newrelic\Exception\DatastoreCallRecordFailedException;

use function error_get_last;
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
     * @template T
     * @param callable(): T $callable
     * @return T
     * @throws DatastoreCallRecordFailedException
     */
    public function record(callable $callable, string $operation)
    {
        error_clear_last();
        $result = @newrelic_record_datastore_segment($callable, $this->params->asSegmentParams($operation));
        if ($result !== false) {
            // Non `false` results are always a success.
            return $result;
        }

        $error = error_get_last();
        if ($error === null) {
            // @phpstan-ignore-next-line No error was raised, which means the callback itself returned `false`.
            return $result;
        }

        // Result is `false`, and an error was raised, convert it to exception.
        throw DatastoreCallRecordFailedException::createFromPhpError($error);
    }
}
