<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\ObjectId;
use MongoDB\BulkWriteResult;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\BulkWrite;
use MongoDB\Tests\CommandObserver;

use function version_compare;

class BulkWriteFunctionalTest extends FunctionalTestCase
{
    /** @var Collection */
    private $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    public function testInserts(): void
    {
        $ops = [
            ['insertOne' => [['_id' => 1, 'x' => 11]]],
            ['insertOne' => [['x' => 22]]],
            ['insertOne' => [(object) ['_id' => 'foo', 'x' => 33]]],
            ['insertOne' => [new BSONDocument(['_id' => 'bar', 'x' => 44])]],
        ];

        $operation = new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), $ops);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(BulkWriteResult::class, $result);
        $this->assertSame(4, $result->getInsertedCount());

        $insertedIds = $result->getInsertedIds();
        $this->assertSame(1, $insertedIds[0]);
        $this->assertInstanceOf(ObjectId::class, $insertedIds[1]);
        $this->assertSame('foo', $insertedIds[2]);
        $this->assertSame('bar', $insertedIds[3]);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => $insertedIds[1], 'x' => 22],
            ['_id' => 'foo', 'x' => 33],
            ['_id' => 'bar', 'x' => 44],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdates(): void
    {
        $this->createFixtures(4);

        $ops = [
            ['updateOne' => [['_id' => 2], ['$inc' => ['x' => 1]]]],
            ['updateMany' => [['_id' => ['$gt' => 2]], ['$inc' => ['x' => -1]]]],
            ['updateOne' => [['_id' => 5], ['$set' => ['x' => 55]], ['upsert' => true]]],
            ['updateOne' => [['x' => 66], ['$set' => ['x' => 66]], ['upsert' => true]]],
            ['updateMany' => [['x' => ['$gt' => 50]], ['$inc' => ['x' => 1]]]],
        ];

        $operation = new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), $ops);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(BulkWriteResult::class, $result);
        $this->assertSame(5, $result->getMatchedCount());
        $this->assertSame(5, $result->getModifiedCount());
        $this->assertSame(2, $result->getUpsertedCount());

        $upsertedIds = $result->getUpsertedIds();
        $this->assertSame(5, $upsertedIds[2]);
        $this->assertInstanceOf(ObjectId::class, $upsertedIds[3]);

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 32],
            ['_id' => 4, 'x' => 43],
            ['_id' => 5, 'x' => 56],
            ['_id' => $upsertedIds[3], 'x' => 67],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testDeletes(): void
    {
        $this->createFixtures(4);

        $ops = [
            ['deleteOne' => [['_id' => 1]]],
            ['deleteMany' => [['_id' => ['$gt' => 2]]]],
        ];

        $operation = new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), $ops);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(BulkWriteResult::class, $result);
        $this->assertSame(3, $result->getDeletedCount());

        $expected = [
            ['_id' => 2, 'x' => 22],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testMixedOrderedOperations(): void
    {
        $this->createFixtures(3);

        $ops = [
            ['updateOne' => [['_id' => ['$gt' => 1]], ['$inc' => ['x' => 1]]]],
            ['updateMany' => [['_id' => ['$gt' => 1]], ['$inc' => ['x' => 1]]]],
            ['insertOne' => [['_id' => 4, 'x' => 44]]],
            ['deleteMany' => [['x' => ['$nin' => [24, 34]]]]],
            ['replaceOne' => [['_id' => 4], ['_id' => 4, 'x' => 44], ['upsert' => true]]],
        ];

        $operation = new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), $ops);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(BulkWriteResult::class, $result);

        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame([2 => 4], $result->getInsertedIds());

        $this->assertSame(3, $result->getMatchedCount());
        $this->assertSame(3, $result->getModifiedCount());
        $this->assertSame(1, $result->getUpsertedCount());
        $this->assertSame([4 => 4], $result->getUpsertedIds());

        $this->assertSame(2, $result->getDeletedCount());

        $expected = [
            ['_id' => 2, 'x' => 24],
            ['_id' => 3, 'x' => 34],
            ['_id' => 4, 'x' => 44],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUnacknowledgedWriteConcern()
    {
        $ops = [['insertOne' => [['_id' => 1]]]];
        $options = ['writeConcern' => new WriteConcern(0)];
        $operation = new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), $ops, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesDeletedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getDeletedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesInsertCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getInsertedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesMatchedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getMatchedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesModifiedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getModifiedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesUpsertedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesUpsertedIds(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedIds();
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['insertOne' => [['_id' => 1]]]],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    public function testBypassDocumentValidationSetWhenTrue(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['insertOne' => [['_id' => 1]]]],
                    ['bypassDocumentValidation' => true]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
                $this->assertEquals(true, $event['started']->getCommand()->bypassDocumentValidation);
            }
        );
    }

    public function testBypassDocumentValidationUnsetWhenFalse(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['insertOne' => [['_id' => 1]]]],
                    ['bypassDocumentValidation' => false]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            }
        );
    }

    public function testBulkWriteWithPipelineUpdates(): void
    {
        if (version_compare($this->getServerVersion(), '4.2.0', '<')) {
            $this->markTestSkipped('Pipeline-style updates are not supported');
        }

        $this->createFixtures(4);

        $ops = [
            ['updateOne' => [['_id' => 2], [['$addFields' => ['y' => 2]]]]],
            ['updateMany' => [['_id' => ['$gt' => 2]], [['$addFields' => ['y' => '$_id']]]]],
        ];

        $operation = new BulkWrite($this->getDatabaseName(), $this->getCollectionName(), $ops);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(BulkWriteResult::class, $result);
        $this->assertSame(3, $result->getMatchedCount());
        $this->assertSame(3, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22, 'y' => 2],
            ['_id' => 3, 'x' => 33, 'y' => 3],
            ['_id' => 4, 'x' => 44, 'y' => 4],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures(int $n): void
    {
        $bulkWrite = new Bulk(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
