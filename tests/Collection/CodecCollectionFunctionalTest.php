<?php

namespace MongoDB\Tests\Collection;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\BulkWriteResult;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;

class CodecCollectionFunctionalTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection(
            $this->manager,
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['codec' => new TestDocumentCodec()],
        );
    }

    public static function provideAggregateOptions(): Generator
    {
        yield 'Default codec' => [
            'expected' => [
                TestObject::createDecodedForFixture(2),
                TestObject::createDecodedForFixture(3),
            ],
            'options' => [],
        ];

        yield 'No codec' => [
            'expected' => [
                self::createFixtureResult(2),
                self::createFixtureResult(3),
            ],
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => [
                Document::fromPHP(self::createFixtureResult(2)),
                Document::fromPHP(self::createFixtureResult(3)),
            ],
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideAggregateOptions */
    public function testAggregate($expected, $options): void
    {
        $this->createFixtures(3);

        $cursor = $this->collection->aggregate([['$match' => ['_id' => ['$gt' => 1]]]], $options);

        $this->assertEquals($expected, $cursor->toArray());
    }

    public function testAggregateWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->aggregate([['$match' => ['_id' => ['$gt' => 1]]]], $options);
    }

    public static function provideBulkWriteOptions(): Generator
    {
        $replacedObject = TestObject::createDecodedForFixture(3);
        $replacedObject->x->foo = 'baz';

        yield 'Default codec' => [
            'expected' => [
                TestObject::createDecodedForFixture(1),
                TestObject::createDecodedForFixture(2),
                $replacedObject,
                TestObject::createDecodedForFixture(4),
            ],
            'options' => [],
        ];

        $replacedObject = new BSONDocument(['_id' => 3, 'id' => 3, 'x' => new BSONDocument(['foo' => 'baz']), 'decoded' => false]);
        $replacedObject->x->foo = 'baz';

        yield 'No codec' => [
            'expected' => [
                self::createFixtureResult(1),
                self::createFixtureResult(2),
                $replacedObject,
                self::createObjectFixtureResult(4, true),
            ],
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => [
                Document::fromPHP(self::createFixtureResult(1)),
                Document::fromPHP(self::createFixtureResult(2)),
                Document::fromPHP($replacedObject),
                Document::fromPHP(self::createObjectFixtureResult(4, true)),
            ],
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideBulkWriteOptions */
    public function testBulkWrite($expected, $options): void
    {
        $this->createFixtures(3);

        $replaceObject = TestObject::createForFixture(3);
        $replaceObject->x->foo = 'baz';

        $operations = [
            ['insertOne' => [TestObject::createForFixture(4)]],
            ['replaceOne' => [['_id' => 3], $replaceObject]],
        ];

        $result = $this->collection->bulkWrite($operations, $options);

        $this->assertInstanceOf(BulkWriteResult::class, $result);
        $this->assertSame(1, $result->getInsertedCount());
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());

        // Extract inserted ID when not using codec as it's an automatically generated ObjectId
        if ($expected[3] instanceof BSONDocument && $expected[3]->_id === null) {
            $expected[3]->_id = $result->getInsertedIds()[0];
        }

        if ($expected[3] instanceof Document && $expected[3]->get('_id') === null) {
            $inserted = $expected[3]->toPHP();
            $inserted->_id = $result->getInsertedIds()[0];
            $expected[3] = Document::fromPHP($inserted);
        }

        $this->assertEquals(
            $expected,
            $this->collection->find([], $options)->toArray(),
        );
    }

    public function provideFindOneAndModifyOptions(): Generator
    {
        yield 'Default codec' => [
            'expected' => TestObject::createDecodedForFixture(1),
            'options' => [],
        ];

        yield 'No codec' => [
            'expected' => self::createFixtureResult(1),
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => Document::fromPHP(self::createFixtureResult(1)),
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideFindOneAndModifyOptions */
    public function testFindOneAndDelete($expected, $options): void
    {
        $this->createFixtures(1);

        $result = $this->collection->findOneAndDelete(['_id' => 1], $options);

        self::assertEquals($expected, $result);
    }

    public function testFindOneAndDeleteWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->findOneAndDelete(['_id' => 1], $options);
    }

    /** @dataProvider provideFindOneAndModifyOptions */
    public function testFindOneAndUpdate($expected, $options): void
    {
        $this->createFixtures(1);

        $result = $this->collection->findOneAndUpdate(['_id' => 1], ['$set' => ['x.foo' => 'baz']], $options);

        self::assertEquals($expected, $result);
    }

    public function testFindOneAndUpdateWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->findOneAndUpdate(['_id' => 1], ['$set' => ['x.foo' => 'baz']], $options);
    }

    public static function provideFindOneAndReplaceOptions(): Generator
    {
        $replacedObject = TestObject::createDecodedForFixture(1);
        $replacedObject->x->foo = 'baz';

        yield 'Default codec' => [
            'expected' => $replacedObject,
            'options' => [],
        ];

        $replacedObject = self::createObjectFixtureResult(1);
        $replacedObject->x->foo = 'baz';

        yield 'No codec' => [
            'expected' => $replacedObject,
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => Document::FromPHP($replacedObject),
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideFindOneAndReplaceOptions */
    public function testFindOneAndReplace($expected, $options): void
    {
        $this->createFixtures(1);

        $replaceObject = TestObject::createForFixture(1);
        $replaceObject->x->foo = 'baz';

        $result = $this->collection->findOneAndReplace(
            ['_id' => 1],
            $replaceObject,
            $options + ['returnDocument' => FindOneAndReplace::RETURN_DOCUMENT_AFTER],
        );

        self::assertEquals($expected, $result);
    }

    public function testFindOneAndReplaceWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->findOneAndReplace(['_id' => 1], TestObject::createForFixture(1), $options);
    }

    public static function provideFindOptions(): Generator
    {
        yield 'Default codec' => [
            'expected' => [
                TestObject::createDecodedForFixture(1),
                TestObject::createDecodedForFixture(2),
                TestObject::createDecodedForFixture(3),
            ],
            'options' => [],
        ];

        yield 'No codec' => [
            'expected' => [
                self::createFixtureResult(1),
                self::createFixtureResult(2),
                self::createFixtureResult(3),
            ],
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => [
                Document::fromPHP(self::createFixtureResult(1)),
                Document::fromPHP(self::createFixtureResult(2)),
                Document::fromPHP(self::createFixtureResult(3)),
            ],
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideFindOptions */
    public function testFind($expected, $options): void
    {
        $this->createFixtures(3);

        $cursor = $this->collection->find([], $options);

        $this->assertEquals($expected, $cursor->toArray());
    }

    public function testFindWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->find([], $options);
    }

    public static function provideFindOneOptions(): Generator
    {
        yield 'Default codec' => [
            'expected' => TestObject::createDecodedForFixture(1),
            'options' => [],
        ];

        yield 'No codec' => [
            'expected' => self::createFixtureResult(1),
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => Document::fromPHP(self::createFixtureResult(1)),
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideFindOneOptions */
    public function testFindOne($expected, $options): void
    {
        $this->createFixtures(1);

        $document = $this->collection->findOne([], $options);

        $this->assertEquals($expected, $document);
    }

    public function testFindOneWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->findOne([], $options);
    }

    public static function provideInsertManyOptions(): Generator
    {
        yield 'Default codec' => [
            'expected' => [
                TestObject::createDecodedForFixture(1),
                TestObject::createDecodedForFixture(2),
                TestObject::createDecodedForFixture(3),
            ],
            'options' => [],
        ];

        yield 'No codec' => [
            'expected' => [
                self::createObjectFixtureResult(1, true),
                self::createObjectFixtureResult(2, true),
                self::createObjectFixtureResult(3, true),
            ],
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => [
                Document::fromPHP(self::createObjectFixtureResult(1, true)),
                Document::fromPHP(self::createObjectFixtureResult(2, true)),
                Document::fromPHP(self::createObjectFixtureResult(3, true)),
            ],
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideInsertManyOptions */
    public function testInsertMany($expected, $options): void
    {
        $documents = [
            TestObject::createForFixture(1),
            TestObject::createForFixture(2),
            TestObject::createForFixture(3),
        ];

        $result = $this->collection->insertMany($documents, $options);
        $this->assertSame(3, $result->getInsertedCount());

        // Add missing identifiers. This is relevant for the "No codec" data set, as the encoded document will not have
        // an "_id" field and the driver will automatically generate one.
        foreach ($expected as $index => &$expectedDocument) {
            if ($expectedDocument instanceof BSONDocument && $expectedDocument->_id === null) {
                $expectedDocument->_id = $result->getInsertedIds()[$index];
            }

            if ($expectedDocument instanceof Document && $expectedDocument->get('_id') === null) {
                $inserted = $expectedDocument->toPHP();
                $inserted->_id = $result->getInsertedIds()[$index];
                $expectedDocument = Document::fromPHP($inserted);
            }
        }

        $this->assertEquals($expected, $this->collection->find([], $options)->toArray());
    }

    public static function provideInsertOneOptions(): Generator
    {
        yield 'Default codec' => [
            'expected' => TestObject::createDecodedForFixture(1),
            'options' => [],
        ];

        yield 'No codec' => [
            'expected' => self::createObjectFixtureResult(1, true),
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => Document::fromPHP(self::createObjectFixtureResult(1, true)),
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideInsertOneOptions */
    public function testInsertOne($expected, $options): void
    {
        $result = $this->collection->insertOne(TestObject::createForFixture(1), $options);
        $this->assertSame(1, $result->getInsertedCount());

        // Add missing identifiers. This is relevant for the "No codec" data set, as the encoded document will have an
        // automatically generated identifier, which needs to be used in the expected document.
        if ($expected instanceof BSONDocument && $expected->_id === null) {
            $expected->_id = $result->getInsertedId();
        }

        if ($expected instanceof Document && $expected->get('_id') === null) {
            $inserted = $expected->toPHP();
            $inserted->_id = $result->getInsertedId();
            $expected = Document::fromPHP($inserted);
        }

        $this->assertEquals($expected, $this->collection->findOne([], $options));
    }

    public static function provideReplaceOneOptions(): Generator
    {
        $replacedObject = TestObject::createDecodedForFixture(1);
        $replacedObject->x->foo = 'baz';

        yield 'Default codec' => [
            'expected' => $replacedObject,
            'options' => [],
        ];

        $replacedObject = self::createObjectFixtureResult(1);
        $replacedObject->x->foo = 'baz';

        yield 'No codec' => [
            'expected' => $replacedObject,
            'options' => ['codec' => null],
        ];

        yield 'BSON type map' => [
            'expected' => Document::fromPHP($replacedObject),
            'options' => ['typeMap' => ['root' => 'bson']],
        ];
    }

    /** @dataProvider provideReplaceOneOptions */
    public function testReplaceOne($expected, $options): void
    {
        $this->createFixtures(1);

        $replaceObject = TestObject::createForFixture(1);
        $replaceObject->x->foo = 'baz';

        $result = $this->collection->replaceOne(['_id' => 1], $replaceObject, $options);
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());

        $this->assertEquals($expected, $this->collection->findOne([], $options));
    }

    public function testReplaceOneWithCodecAndTypemap(): void
    {
        $options = [
            'codec' => new TestDocumentCodec(),
            'typeMap' => ['root' => 'array', 'document' => 'array'],
        ];

        $this->expectExceptionObject(InvalidArgumentException::cannotCombineCodecAndTypeMap());
        $this->collection->replaceOne(['_id' => 1], ['foo' => 'bar'], $options);
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n, array $executeBulkWriteOptions = []): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert(TestObject::createDocument($i));
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite, $executeBulkWriteOptions);

        $this->assertEquals($n, $result->getInsertedCount());
    }

    private static function createFixtureResult(int $id): BSONDocument
    {
        return new BSONDocument(['_id' => $id, 'x' => new BSONDocument(['foo' => 'bar'])]);
    }

    private static function createObjectFixtureResult(int $id, bool $isInserted = false): BSONDocument
    {
        return new BSONDocument([
            '_id' => $isInserted ? null : $id,
            'id' => $id,
            'x' => new BSONDocument(['foo' => 'bar']),
            'decoded' => false,
        ]);
    }
}
