<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelic;

use Psr\SimpleCache\CacheInterface;
use Samuelnogueira\CacheDatastoreNewrelic\Newrelic\DatastoreCallRecorder;

/**
 * @api
 */
final class SimpleCacheDecorator implements CacheInterface
{
    private CacheInterface $wrapped;
    private DatastoreCallRecorder $recorder;

    public function __construct(CacheInterface $wrapped, DatastoreParams $params)
    {
        $this->wrapped  = $wrapped;
        $this->recorder = new DatastoreCallRecorder($params);
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->recorder->record(
            function () use ($key, $default) {
                return $this->wrapped->get($key, $default);
            },
            'get'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function set($key, $value, $ttl = null): bool
    {
        return $this->recorder->record(
            function () use ($key, $value, $ttl): bool {
                return $this->wrapped->set($key, $value, $ttl);
            },
            'set'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function delete($key): bool
    {
        return $this->recorder->record(
            function () use ($key): bool {
                return $this->wrapped->delete($key);
            },
            'delete'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function clear(): bool
    {
        return $this->recorder->record(
            function (): bool {
                return $this->wrapped->clear();
            },
            'clear'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function getMultiple($keys, $default = null): iterable
    {
        return $this->recorder->record(
            function () use ($keys, $default): iterable {
                return $this->wrapped->getMultiple($keys, $default);
            },
            'mget'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function setMultiple($values, $ttl = null): bool
    {
        return $this->recorder->record(
            function () use ($values, $ttl): bool {
                return $this->wrapped->setMultiple($values, $ttl);
            },
            'mset'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function deleteMultiple($keys): bool
    {
        return $this->recorder->record(
            function () use ($keys): bool {
                return $this->wrapped->deleteMultiple($keys);
            },
            'mdelete'
        );
    }

    /**
     * @inheritDoc
     * @throws Newrelic\Exception\DatastoreCallRecordFailedException
     */
    public function has($key): bool
    {
        return $this->recorder->record(
            function () use ($key): bool {
                return $this->wrapped->has($key);
            },
            'has'
        );
    }
}
