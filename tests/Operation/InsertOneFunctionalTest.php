<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\InsertOneResult;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\InsertOne;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;
use stdClass;

class InsertOneFunctionalTest extends FunctionalTestCase
{
    private Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * @dataProvider provideDocumentsWithIds
     * @dataProvider provideDocumentsWithoutIds
     */
    public function testDocumentEncoding($document, stdClass $expectedDocument): void
    {
        (new CommandObserver())->observe(
            function () use ($document, $expectedDocument): void {
                $operation = new InsertOne(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    $document,
                );

                $result = $operation->execute($this->getPrimaryServer());

                // Replace _id placeholder if necessary
                if ($expectedDocument->_id === null) {
                    $expectedDocument->_id = $result->getInsertedId();
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

    /** @dataProvider provideDocumentsWithIds */
    public function testInsertOneWithExistingId($document, stdClass $expectedDocument): void
    {
        $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(InsertOneResult::class, $result);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame($expectedDocument->_id, $result->getInsertedId());

        $this->assertSameDocuments([$expectedDocument], $this->collection->find());
    }

    /** @dataProvider provideDocumentsWithoutIds */
    public function testInsertOneWithGeneratedId($document, stdClass $expectedDocument): void
    {
        $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(InsertOneResult::class, $result);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertInstanceOf(ObjectId::class, $result->getInsertedId());

        // Replace _id placeholder
        $expectedDocument->_id = $result->getInsertedId();

        $this->assertSameDocuments([$expectedDocument], $this->collection->find());
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new InsertOne(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
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
                $operation = new InsertOne(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
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
                $operation = new InsertOne(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
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
        $document = ['x' => 11];
        $options = ['writeConcern' => new WriteConcern(0)];

        $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesInsertedCount(InsertOneResult $result): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getInsertedCount();
    }

    /** @depends testUnacknowledgedWriteConcern */
    public function testUnacknowledgedWriteConcernAccessesInsertedId(InsertOneResult $result): void
    {
        $this->assertInstanceOf(ObjectId::class, $result->getInsertedId());
    }

    public function testInsertingWithCodec(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $document = TestObject::createForFixture(1);
                $options = ['codec' => new TestDocumentCodec()];

                $operation = new InsertOne($this->getDatabaseName(), $this->getCollectionName(), $document, $options);
                $result = $operation->execute($this->getPrimaryServer());

                $this->assertSame(1, $result->getInsertedId());
            },
            function (array $event): void {
                $this->assertEquals(
                    (object) [
                        '_id' => 1,
                        'x' => (object) ['foo' => 'bar'],
                        'encoded' => true,
                    ],
                    $event['started']->getCommand()->documents[0] ?? null,
                );
            },
        );
    }
}
