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
    /** @var CacheInterface */
    private $wrapped;
    /** @var DatastoreCallRecorder */
    private $recorder;

    public function __construct(CacheInterface $wrapped, DatastoreParams $params)
    {
        $this->wrapped  = $wrapped;
        $this->recorder = new DatastoreCallRecorder($params);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
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
