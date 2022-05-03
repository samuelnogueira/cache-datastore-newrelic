<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelicTests\Stubs;

use function array_shift;
use function trigger_error;

/**
 * @internal
 */
final class NewrelicMock
{
    /** @var array<array<string, string>> */
    private static array $recordedSegments = [];
    /** @var array<int, array{'message': string, 'level': int}> */
    private static array $upcomingErrors = [];

    /**
     * @param array<string, string> $params
     * @return false|mixed
     */
    public static function recordDatastoreSegment(callable $callable, array $params): mixed
    {
        if (self::$upcomingErrors !== []) {
            ['message' => $message, 'level' => $level] = array_shift(self::$upcomingErrors);
            trigger_error($message, $level);

            return false;
        }

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

    public static function addUpcomingError(string $message, int $level): void
    {
        self::$upcomingErrors[] = ['message' => $message, 'level' => $level];
    }

    public static function reset(): void
    {
        self::$recordedSegments = [];
        self::$upcomingErrors   = [];
    }
}
