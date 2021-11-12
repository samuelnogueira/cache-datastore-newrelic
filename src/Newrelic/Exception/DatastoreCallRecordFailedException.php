<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelic\Newrelic\Exception;

use ErrorException;
use Psr\SimpleCache\CacheException;

final class DatastoreCallRecordFailedException extends ErrorException implements CacheException
{
    /**
     * @param array{'message': string, 'type': int} $error
     */
    public static function createFromPhpError(array $error): self
    {
        ['message' => $message, 'type' => $type] = $error;

        return new self($message, 0, $type);
    }
}
