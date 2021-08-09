<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelic;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Samuelnogueira\CacheDatastoreNewrelic\Newrelic\DatastoreCallRecorder;

/**
 * @api
 */
final class CacheItemPoolDecorator implements CacheItemPoolInterface
{
    /** @var CacheItemPoolInterface */
    private $wrapped;
    /** @var DatastoreCallRecorder */
    private $recorder;

    public function __construct(CacheItemPoolInterface $wrapped, DatastoreParams $params)
    {
        $this->wrapped  = $wrapped;
        $this->recorder = new DatastoreCallRecorder($params);
    }


    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface
    {
        return $this->recorder->record(
            function () use ($key): CacheItemInterface {
                return $this->wrapped->getItem($key);
            },
            'get'
        );
    }

    /**
     * @inheritDoc
     *
     * @return iterable<string, CacheItemInterface>
     */
    public function getItems(array $keys = []): iterable
    {
        return $this->recorder->record(
            function () use ($keys): iterable {
                return $this->wrapped->getItems($keys);
            },
            'mget'
        );
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        return $this->recorder->record(
            function () use ($key): bool {
                return $this->wrapped->hasItem($key);
            },
            'has'
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
    public function deleteItem($key): bool
    {
        return $this->recorder->record(
            function () use ($key): bool {
                return $this->wrapped->deleteItem($key);
            },
            'delete'
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        return $this->recorder->record(
            function () use ($keys): bool {
                return $this->wrapped->deleteItems($keys);
            },
            'mdelete'
        );
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        return $this->recorder->record(
            function () use ($item): bool {
                return $this->wrapped->save($item);
            },
            'save'
        );
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        // This operation is usually done without calling the cache service, so we shouldn't trace it.
        return $this->wrapped->saveDeferred($item);
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->recorder->record(
            function (): bool {
                return $this->wrapped->commit();
            },
            'commit'
        );
    }
}
