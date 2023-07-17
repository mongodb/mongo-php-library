<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\InsertManyResult;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\InsertMany;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;

class InsertManyFunctionalTest extends FunctionalTestCase
{
    private Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    public function testDocumentEncoding(): void
    {
        $documents = [
            ['_id' => 1],
            (object) ['_id' => 2],
            new BSONDocument(['_id' => 3]),
            Document::fromPHP(['_id' => 4]),
            ['x' => 1],
            (object) ['x' => 2],
            new BSONDocument(['x' => 3]),
            Document::fromPHP(['x' => 4]),
        ];

        $expectedDocuments = [
            (object) ['_id' => 1],
            (object) ['_id' => 2],
            (object) ['_id' => 3],
            (object) ['_id' => 4],
            // Note: _id placeholders must be replaced with generated ObjectIds
            (object) ['_id' => null, 'x' => 1],
            (object) ['_id' => null, 'x' => 2],
            (object) ['_id' => null, 'x' => 3],
            (object) ['_id' => null, 'x' => 4],
        ];

        (new CommandObserver())->observe(
            function () use ($documents, $expectedDocuments): void {
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    $documents,
                );

                $result = $operation->execute($this->getPrimaryServer());
                $insertedIds = $result->getInsertedIds();

                foreach ($expectedDocuments as $i => $expectedDocument) {
                    // Replace _id placeholder if necessary
                    if ($expectedDocument->_id === null) {
                        $expectedDocument->_id = $insertedIds[$i];
                    }
                }
            },
            function (array $event) use ($expectedDocuments): void {
                $this->assertEquals($expectedDocuments, $event['started']->getCommand()->documents ?? null);
            },
        );
    }

    public function testInsertMany(): void
    {
        $documents = [
            ['_id' => 1],
            (object) ['_id' => 2],
            new BSONDocument(['_id' => 3]),
            Document::fromPHP(['_id' => 4]),
            ['x' => 1],
            (object) ['x' => 2],
            new BSONDocument(['x' => 3]),
            Document::fromPHP(['x' => 4]),
        ];

        $operation = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), $documents);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(InsertManyResult::class, $result);
        $this->assertSame(8, $result->getInsertedCount());

        $insertedIds = $result->getInsertedIds();
        $this->assertSame(1, $insertedIds[0]);
        $this->assertSame(2, $insertedIds[1]);
        $this->assertSame(3, $insertedIds[2]);
        $this->assertSame(4, $insertedIds[3]);
        $this->assertInstanceOf(ObjectId::class, $insertedIds[4]);
        $this->assertInstanceOf(ObjectId::class, $insertedIds[5]);
        $this->assertInstanceOf(ObjectId::class, $insertedIds[6]);
        $this->assertInstanceOf(ObjectId::class, $insertedIds[7]);

        $expected = [
            ['_id' => 1],
            ['_id' => 2],
            ['_id' => 3],
            ['_id' => 4],
            ['_id' => $insertedIds[4], 'x' => 1],
            ['_id' => $insertedIds[5], 'x' => 2],
            ['_id' => $insertedIds[6], 'x' => 3],
            ['_id' => $insertedIds[7], 'x' => 4],

        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['_id' => 1], ['_id' => 2]],
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
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['_id' => 1], ['_id' => 2]],
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
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['_id' => 1], ['_id' => 2]],
                    ['bypassDocumentValidation' => false],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            },
        );
    }

    public function testUnacknowledgedWriteConcern()
    {
        $documents = [['x' => 11]];
        $options = ['writeConcern' => new WriteConcern(0)];

        $operation = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), $documents, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesInsertedCount(InsertManyResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getInsertedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesInsertedId(InsertManyResult $result): void
    {
        $this->assertInstanceOf(ObjectId::class, $result->getInsertedIds()[0]);
    }

    public function testInsertingWithCodec(): void
    {
        $documents = [
            TestObject::createForFixture(1),
            TestObject::createForFixture(2),
            TestObject::createForFixture(3),
        ];

        $expectedDocuments = [
            (object) [
                '_id' => 1,
                'x' => (object) ['foo' => 'bar'],
                'encoded' => true,
            ],
            (object) [
                '_id' => 2,
                'x' => (object) ['foo' => 'bar'],
                'encoded' => true,
            ],
            (object) [
                '_id' => 3,
                'x' => (object) ['foo' => 'bar'],
                'encoded' => true,
            ],
        ];

        (new CommandObserver())->observe(
            function () use ($documents): void {
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    $documents,
                    ['codec' => new TestDocumentCodec()],
                );

                $result = $operation->execute($this->getPrimaryServer());
                $this->assertEquals([1, 2, 3], $result->getInsertedIds());
            },
            function (array $event) use ($expectedDocuments): void {
                $this->assertEquals($expectedDocuments, $event['started']->getCommand()->documents ?? null);
            },
        );
    }
}
