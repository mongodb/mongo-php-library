<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Operation\Update;
use MongoDB\Tests\CommandObserver;
use MongoDB\UpdateResult;
use stdClass;

use function is_array;

class UpdateFunctionalTest extends FunctionalTestCase
{
    private Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    /** @dataProvider provideFilterDocuments */
    public function testFilterDocuments($filter, stdClass $expectedFilter): void
    {
        (new CommandObserver())->observe(
            function () use ($filter): void {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    $filter,
                    ['$set' => ['x' => 1]],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedFilter): void {
                $this->assertEquals($expectedFilter, $event['started']->getCommand()->updates[0]->q ?? null);
            },
        );
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @dataProvider provideUpdateDocuments
     * @dataProvider provideUpdatePipelines
     * @dataProvider provideReplacementDocumentLikePipeline
     */
    public function testUpdateDocuments($update, $expectedUpdate): void
    {
        if (is_array($expectedUpdate)) {
            $this->skipIfServerVersion('<', '4.2.0', 'Pipeline-style updates are not supported');
        }

        (new CommandObserver())->observe(
            function () use ($update): void {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['x' => 1],
                    $update,
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedUpdate): void {
                $this->assertEquals($expectedUpdate, $event['started']->getCommand()->updates[0]->u ?? null);
            },
        );
    }

    public function provideReplacementDocumentLikePipeline(): array
    {
        /* Note: libmongoc encodes this replacement document as a BSON array
         * because it resembles an update pipeline (see: CDRIVER-4658). */
        return [
            'replacement_like_pipeline' => [
                (object) ['0' => ['$set' => ['x' => 1]]],
                [(object) ['$set' => (object) ['x' => 1]]],
            ],
        ];
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    ['$inc' => ['x' => 1]],
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
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    ['$inc' => ['x' => 1]],
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
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    ['$inc' => ['x' => 1]],
                    ['bypassDocumentValidation' => false],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            },
        );
    }

    public function testHintOptionAndUnacknowledgedWriteConcernUnsupportedClientSideError(): void
    {
        $this->skipIfServerVersion('>=', '4.2.0', 'hint is supported');

        $operation = new Update(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['_id' => 1],
            ['$inc' => ['x' => 1]],
            ['hint' => '_id_', 'writeConcern' => new WriteConcern(0)],
        );

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Hint is not supported by the server executing this operation');

        $operation->execute($this->getPrimaryServer());
    }

    public function testUpdateOne(): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());
        $this->assertSame(0, $result->getUpsertedCount());
        $this->assertNull($result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateMany(): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];
        $options = ['multi' => true];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(2, $result->getMatchedCount());
        $this->assertSame(2, $result->getModifiedCount());
        $this->assertSame(0, $result->getUpsertedCount());
        $this->assertNull($result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 34],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWithExistingId(): void
    {
        $this->createFixtures(3);

        $filter = ['_id' => 5];
        $update = ['$set' => ['x' => 55]];
        $options = ['upsert' => true];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(0, $result->getMatchedCount());
        $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(1, $result->getUpsertedCount());
        $this->assertSame(5, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 5, 'x' => 55],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWithGeneratedId(): void
    {
        $this->createFixtures(3);

        $filter = ['x' => 66];
        $update = ['$set' => ['x' => 66]];
        $options = ['upsert' => true];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(0, $result->getMatchedCount());
        $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(1, $result->getUpsertedCount());
        $this->assertInstanceOf(ObjectId::class, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => $result->getUpsertedId(), 'x' => 66],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUnacknowledgedWriteConcern()
    {
        $filter = ['_id' => 1];
        $update = ['$set' => ['x' => 1]];
        $options = ['writeConcern' => new WriteConcern(0)];
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesMatchedCount(UpdateResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getMatchedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesModifiedCount(UpdateResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getModifiedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesUpsertedCount(UpdateResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesUpsertedId(UpdateResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedId();
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

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
