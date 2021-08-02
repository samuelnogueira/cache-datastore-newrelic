<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelicTests;

use PHPUnit\Framework\TestCase;
use Samuelnogueira\CacheDatastoreNewrelic\CacheItemPoolDecorator;
use Samuelnogueira\CacheDatastoreNewrelic\DatastoreParams;
use Samuelnogueira\CacheDatastoreNewrelicTests\Stubs\NewrelicStub;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use function is_array;
use function iterator_to_array;

final class CacheItemPoolDecoratorTest extends TestCase
{
    public function testAllOperations(): void
    {
        $subject = new CacheItemPoolDecorator(
            new ArrayAdapter(),
            new DatastoreParams('my_product', null, 'my_host'),
        );

        // Writes
        $item1 = $subject->getItem('foo1');
        $item2 = $subject->getItem('foo2');
        $item3 = $subject->getItem('foo3');
        $item4 = $subject->getItem('foo4');
        $item5 = $subject->getItem('foo5');

        $item1->set('foo1_val');
        $item2->set('foo2_val');
        $item3->set('foo3_val');
        $item4->set('foo4_val');
        $item5->set('foo5_val');

        self::assertTrue($subject->clear());
        self::assertTrue($subject->save($item1));
        self::assertTrue($subject->save($item2));
        self::assertTrue($subject->save($item3));
        self::assertTrue($subject->saveDeferred($item4));
        self::assertTrue($subject->saveDeferred($item5));
        self::assertTrue($subject->commit());
        self::assertTrue($subject->deleteItem('foo2'));
        self::assertTrue($subject->deleteItems(['foo3', 'foo4']));

        // Reads
        self::assertTrue($subject->hasItem('foo1'));
        self::assertFalse($subject->hasItem('foo2'));
        self::assertEquals('foo1_val', $subject->getItem('foo1')->get());

        $res = $subject->getItems(['foo1', 'foo2', 'foo5']);
        ['foo1' => $res1, 'foo2' => $res2, 'foo5' => $res3] = is_array($res) ? $res : iterator_to_array($res);
        self::assertEquals('foo1_val', $res1->get());
        self::assertFalse($res2->isHit());
        self::assertEquals('foo5_val', $res3->get());

        self::assertEquals(
            [
                // Writes
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'get'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'get'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'get'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'get'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'get'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'clear'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'save'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'save'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'save'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'commit'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'delete'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'mdelete'],

                // Reads
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'has'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'has'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'get'],
                ['product' => 'my_product', 'host' => 'my_host', 'operation' => 'mget'],
            ],
            NewrelicStub::getRecordedSegments(),
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        NewrelicStub::reset();
    }
}
