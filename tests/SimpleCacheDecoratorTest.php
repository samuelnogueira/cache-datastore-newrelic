<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelicTests;

use PHPUnit\Framework\TestCase;
use Samuelnogueira\CacheDatastoreNewrelic\DatastoreParams;
use Samuelnogueira\CacheDatastoreNewrelic\Newrelic\Exception\DatastoreCallRecordFailedException;
use Samuelnogueira\CacheDatastoreNewrelic\SimpleCacheDecorator;
use Samuelnogueira\CacheDatastoreNewrelicTests\Stubs\NewrelicMock;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

use const E_USER_WARNING;

final class SimpleCacheDecoratorTest extends TestCase
{
    public function testAllOperations(): void
    {
        $cache   = new ArrayAdapter();
        $subject = new SimpleCacheDecorator(
            new Psr16Cache($cache),
            new DatastoreParams('my_product', 'my_collection'),
        );

        // Writes
        self::assertTrue($subject->clear());
        self::assertTrue($subject->set('foo1', 'foo1_val'));
        self::assertTrue($subject->set('foo2', 'foo2_val'));
        self::assertTrue($subject->setMultiple(['bar1' => 'bar1_val', 'bar2' => 'bar2_val', 'bar3' => 'bar3_val']));
        self::assertTrue($subject->delete('foo2'));
        self::assertTrue($subject->deleteMultiple(['bar2', 'bar3']));

        // Reads
        self::assertTrue($subject->has('foo1'));
        self::assertFalse($subject->has('foo2'));
        self::assertEquals('foo1_val', $subject->get('foo1'));
        self::assertEquals(
            ['bar1' => 'bar1_val', 'bar2' => null, 'bar3' => null],
            $subject->getMultiple(['bar1', 'bar2', 'bar3'])
        );

        self::assertEquals(
            [
                // Writes
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'clear'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'set'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'set'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'mset'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'delete'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'mdelete'],

                // Reads
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'has'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'has'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'get'],
                ['product' => 'my_product', 'collection' => 'my_collection', 'operation' => 'mget'],
            ],
            NewrelicMock::getRecordedSegments(),
        );
    }

    public function testFailureThrowsException(): void
    {
        $subject = new SimpleCacheDecorator(
            new Psr16Cache(new ArrayAdapter()),
            new DatastoreParams('reynholm_industries'),
        );
        NewrelicMock::addUpcomingError('Office has too much RAM', E_USER_WARNING);

        $this->expectException(DatastoreCallRecordFailedException::class);
        $this->expectExceptionMessage('Office has too much RAM');

        $subject->get('jen');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        NewrelicMock::reset();
    }
}
