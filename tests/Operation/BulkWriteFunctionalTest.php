<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\ObjectId;
use MongoDB\BulkWriteResult;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite as Bulk;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\BulkWrite;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;
use stdClass;

use function is_array;

class BulkWriteFunctionalTest extends FunctionalTestCase
{
    private Collection $collection;

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

    /**
     * @dataProvider provideDocumentsWithIds
     * @dataProvider provideDocumentsWithoutIds
     */
    public function testInsertDocumentEncoding($document, stdClass $expectedDocument): void
    {
        (new CommandObserver())->observe(
            function () use ($document, $expectedDocument): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['insertOne' => [$document]]],
                );

                $result = $operation->execute($this->getPrimaryServer());

                // Replace _id placeholder if necessary
                if ($expectedDocument->_id === null) {
                    $expectedDocument->_id = $result->getInsertedIds()[0];
                }
            },
            function (array $event) use ($expectedDocument): void {
                $this->assertEquals($expectedDocument, $event['started']->getCommand()->documents[0] ?? null);
            },
        );
    }

    public function provideDocumentsWithIds(): array
    {
        $expectedDocument = (object) ['_id' => 1];

        return [
            'with_id:array' => [['_id' => 1], $expectedDocument],
            'with_id:object' => [(object) ['_id' => 1], $expectedDocument],
            'with_id:Serializable' => [new BSONDocument(['_id' => 1]), $expectedDocument],
            'with_id:Document' => [Document::fromPHP(['_id' => 1]), $expectedDocument],
        ];
    }

    public function provideDocumentsWithoutIds(): array
    {
        /* Note: _id placeholders must be replaced with generated ObjectIds. We
         * also clone the value for each data set since tests may need to modify
         * the object. */
        $expectedDocument = (object) ['_id' => null, 'x' => 1];

        return [
            'without_id:array' => [['x' => 1], clone $expectedDocument],
            'without_id:object' => [(object) ['x' => 1], clone $expectedDocument],
            'without_id:Serializable' => [new BSONDocument(['x' => 1]), clone $expectedDocument],
            'without_id:Document' => [Document::fromPHP(['x' => 1]), clone $expectedDocument],
        ];
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

    /** @dataProvider provideFilterDocuments */
    public function testUpdateFilterDocuments($filter, stdClass $expectedFilter): void
    {
        (new CommandObserver())->observe(
            function () use ($filter): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [
                        ['replaceOne' => [$filter, ['x' => 1]]],
                        ['updateOne' => [$filter, ['$set' => ['x' => 1]]]],
                        ['updateMany' => [$filter, ['$set' => ['x' => 1]]]],
                    ],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedFilter): void {
                $this->assertEquals($expectedFilter, $event['started']->getCommand()->updates[0]->q ?? null);
                $this->assertEquals($expectedFilter, $event['started']->getCommand()->updates[1]->q ?? null);
                $this->assertEquals($expectedFilter, $event['started']->getCommand()->updates[2]->q ?? null);
            },
        );
    }

    /** @dataProvider provideReplacementDocuments */
    public function testReplacementDocuments($replacement, stdClass $expectedReplacement): void
    {
        (new CommandObserver())->observe(
            function () use ($replacement): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['replaceOne' => [['x' => 1], $replacement]]],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedReplacement): void {
                $this->assertEquals($expectedReplacement, $event['started']->getCommand()->updates[0]->u ?? null);
            },
        );
    }

    /**
     * @dataProvider provideUpdateDocuments
     * @dataProvider provideUpdatePipelines
     */
    public function testUpdateDocuments($update, $expectedUpdate): void
    {
        if (is_array($expectedUpdate)) {
            $this->skipIfServerVersion('<', '4.2.0', 'Pipeline-style updates are not supported');
        }

        (new CommandObserver())->observe(
            function () use ($update): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [
                        ['updateOne' => [['x' => 1], $update]],
                        ['updateMany' => [['x' => 1], $update]],
                    ],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedUpdate): void {
                $this->assertEquals($expectedUpdate, $event['started']->getCommand()->updates[0]->u ?? null);
                $this->assertEquals($expectedUpdate, $event['started']->getCommand()->updates[1]->u ?? null);
            },
        );
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

    /** @dataProvider provideFilterDocuments */
    public function testDeleteFilterDocuments($filter, stdClass $expectedQuery): void
    {
        (new CommandObserver())->observe(
            function () use ($filter): void {
                $operation = new BulkWrite(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [
                        ['deleteOne' => [$filter]],
                        ['deleteMany' => [$filter]],
                    ],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedQuery): void {
                $this->assertEquals($expectedQuery, $event['started']->getCommand()->deletes[0]->q ?? null);
                $this->assertEquals($expectedQuery, $event['started']->getCommand()->deletes[1]->q ?? null);
            },
        );
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

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesDeletedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getDeletedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesInsertCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getInsertedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesMatchedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getMatchedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesModifiedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getModifiedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesUpsertedCount(BulkWriteResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
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
                    ['session' => $this->createSession()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            },
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
                    ['bypassDocumentValidation' => true],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
                $this->assertEquals(true, $event['started']->getCommand()->bypassDocumentValidation);
            },
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
                    ['bypassDocumentValidation' => false],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            },
        );
    }

    public function testBulkWriteWithPipelineUpdates(): void
    {
        $this->skipIfServerVersion('<', '4.2.0', 'Pipeline-style updates are not supported');

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

    public function testCodecOption(): void
    {
        $this->createFixtures(3);

        $codec = new TestDocumentCodec();

        $replaceObject = TestObject::createForFixture(3);
        $replaceObject->x->foo = 'baz';

        $operations = [
            ['insertOne' => [TestObject::createForFixture(4)]],
            ['replaceOne' => [['_id' => 3], $replaceObject]],
        ];

        $operation = new BulkWrite(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            $operations,
            ['codec' => $codec],
        );

        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(BulkWriteResult::class, $result);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());

        $replacedObject = TestObject::createDecodedForFixture(3);
        $replacedObject->x->foo = 'baz';

        // Only read the last two documents as the other two don't fit our codec
        $this->assertEquals(
            [
                $replacedObject,
                TestObject::createDecodedForFixture(4),
            ],
            $this->collection->find(['_id' => ['$gte' => 3]], ['codec' => $codec])->toArray(),
        );
    }

    /**
     * Create data fixtures.
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
