<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelicTests\Stubs;

/**
 * @internal
 */
final class NewrelicStub
{
    /** @var array<array<string, string>> */
    private static $recordedSegments = [];

    /**
     * @param array<string, string> $params
     * @return false|mixed
     */
    public static function recordDatastoreSegment(callable $callable, array $params)
    {
        self::$recordedSegments[] = $params;

        return $callable();
    }

    /**
     * @return array<array<string, string>>
     */
    public static function getRecordedSegments(): array
    {
        return self::$recordedSegments;
    }

    public static function reset(): void
    {
        self::$recordedSegments = [];
    }
}
