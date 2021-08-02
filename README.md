# cache-datastore-newrelic
Reports calls to any `psr/simple-cache` or `psr/cache` implementation as a custom New Relic Datastore.

Uses the [newrelic_record_datastore_segment](https://docs.newrelic.com/docs/agents/php-agent/php-agent-api/newrelic_record_datastore_segment/) function to record calls to an unsupported database.

## Requirements

Requires New Relic PHP Agent version >= 7.5.0.

## Usage

Decorate your existing:
1. `\Psr\Cache\CacheItemPoolInterface`
2. `\Psr\SimpleCache\CacheInterface` 
  
with respectively:
1. `Samuelnogueira\CacheDatastoreNewrelic\CacheItemPoolDecorator`
2. `Samuelnogueira\CacheDatastoreNewrelic\SimpleCacheDecorator`

Example with [PSR-6: Caching Interface](https://www.php-fig.org/psr/psr-6/):

```php
use Psr\Cache\CacheItemPoolInterface;
use Samuelnogueira\CacheDatastoreNewrelic\CacheItemPoolDecorator;
use Samuelnogueira\CacheDatastoreNewrelic\DatastoreParams;

/** @var CacheItemPoolInterface $cache */

return new CacheItemPoolDecorator(
    $cache, // your cache adapter
    new DatastoreParams('My Database Product')
);
```

Example with [PSR-16: Common Interface for Caching Libraries](https://www.php-fig.org/psr/psr-16/) (aka Simple Cache):

```php
use Psr\SimpleCache\CacheInterface;
use Samuelnogueira\CacheDatastoreNewrelic\SimpleCacheDecorator;
use Samuelnogueira\CacheDatastoreNewrelic\DatastoreParams;

/** @var CacheInterface $cache */

return new SimpleCacheDecorator(
    $cache, // your cache adapter
    new DatastoreParams('My Database Product')
);
```

Additional params about the data store can be