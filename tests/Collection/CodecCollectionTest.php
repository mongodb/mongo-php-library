<?php

namespace MongoDB\Tests\Collection;

use MongoDB\BSON\Document;
use MongoDB\Codec\Codec;
use MongoDB\Collection;

use function iterator_to_array;

class CodecCollectionTest extends FunctionalTestCase
{
    /** @var Codec */
    private $codec;

    /** @var Collection */
    private $codecCollection;

    public function setUp(): void
    {
        parent::setUp();

        $this->codec = $this->createCodec();

        $this->codecCollection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName(), ['codec' => $this->codec]);
    }

    public function testWithoutCollectionCodec(): void
    {
        $document = new CollectionTestModel((object) ['foo' => 'bar']);
        $this->collection->insertOne($document);

        $this->assertMatchesDocument(
            ['data' => ['foo' => 'bar']],
            $this->collection->findOne()
        );
    }

    public function testOperationCodecOverridesCollection(): void
    {
        $this->collection->insertOne(['_id' => 1, 'foo' => 'bar']);

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 1, 'foo' => 'bar']),
            $this->collection->findOne(['_id' => 1], ['codec' => $this->createCodec()])
        );
    }

    public function testOperationTypeMapOverridesCollectionCodec(): void
    {
        $this->createTestFixtures();

        $this->assertIsArray($this->codecCollection->findOne(['_id' => 2], ['typeMap' => ['root' => 'array']]));
    }

    public function testOperationCodecOverridesCollectionCodec(): void
    {
        $this->createTestFixtures();

        $codec = $this->createCodec('operation');

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz'], 'operation'),
            $this->codecCollection->findOne(['_id' => 2], ['codec' => $codec])
        );
    }

    public function testOperationCodecTakesPrecedenceOverOperationTypemap(): void
    {
        $this->createTestFixtures();

        $codec = $this->createCodec('operation');

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz'], 'operation'),
            $this->codecCollection->findOne(['_id' => 2], ['codec' => $codec, 'typeMap' => ['root' => 'array']])
        );
    }

    public function testInsertOne(): void
    {
        $document = new CollectionTestModel((object) ['foo' => 'bar']);
        $this->codecCollection->insertOne($document);

        $this->assertMatchesDocument(
            ['foo' => 'bar'],
            $this->collection->findOne()
        );
    }

    public function testInsertMany(): void
    {
        $document = new CollectionTestModel((object) ['foo' => 'bar']);
        $this->codecCollection->insertMany([$document, $document]);

        $result = iterator_to_array($this->collection->find());
        $this->assertCount(2, $result);
        $this->assertMatchesDocument(
            ['foo' => 'bar'],
            $result[0]
        );
        $this->assertMatchesDocument(
            ['foo' => 'bar'],
            $result[1]
        );
    }

    public function testFindOne(): void
    {
        $this->createTestFixtures();

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
            $this->codecCollection->findOne(['_id' => 2])
        );
    }

    public function testFind(): void
    {
        $this->createTestFixtures();

        $this->assertEquals(
            [
                new CollectionTestModel((object) ['_id' => 1, 'foo' => 'bar']),
                new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
            ],
            iterator_to_array($this->codecCollection->find(['_id' => ['$lt' => 3]]))
        );
    }

    public function testAggregate(): void
    {
        $this->createTestFixtures();

        $this->assertEquals(
            [
                new CollectionTestModel((object) ['_id' => 1, 'foo' => 'bar']),
                new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
            ],
            iterator_to_array($this->codecCollection->aggregate([['$match' => ['_id' => ['$lt' => 3]]]]))
        );
    }

    public function testFindOneAndDelete(): void
    {
        $this->createTestFixtures();

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
            $this->codecCollection->findOneAndDelete(['_id' => 2])
        );
    }

    public function testFindOneAndReplace(): void
    {
        $this->createTestFixtures();

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
            $this->codecCollection->findOneAndReplace(['_id' => 2], new CollectionTestModel((object) ['_id' => 2, 'baz' => 'foo']))
        );
    }

    public function testFindOneAndUpdate(): void
    {
        $this->createTestFixtures();

        $this->assertEquals(
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
            $this->codecCollection->findOneAndUpdate(['_id' => 2], ['$set' => ['baz' => 'foo']])
        );
    }

    public function testReplaceOne(): void
    {
        $this->createTestFixtures();

        $document = new CollectionTestModel((object) ['_id' => 2, 'baz' => 'foo']);
        $this->codecCollection->replaceOne(['_id' => 2], $document);

        $this->assertSameDocument(
            ['_id' => 2, 'baz' => 'foo'],
            $this->collection->findOne(['_id' => 2])
        );
    }

    private function createCodec(?string $marker = null): Codec
    {
        return new class ($marker) implements Codec {
            /** @var string|null */
            private $marker;

            public function __construct(?string $marker = null)
            {
                $this->marker = $marker;
            }

            public function canDecode($value): bool
            {
                return $value instanceof Document;
            }

            public function canEncode($value): bool
            {
                return $value instanceof CollectionTestModel;
            }

            public function decode($value): ?CollectionTestModel
            {
                if (! $value instanceof Document) {
                    return null;
                }

                return new CollectionTestModel($value->toPHP(), $this->marker);
            }

            public function encode($value): ?Document
            {
                if (! $value instanceof CollectionTestModel) {
                    return null;
                }

                return Document::fromPHP($value->data);
            }
        };
    }

    private function createTestFixtures(): void
    {
        $this->codecCollection->insertMany([
            new CollectionTestModel((object) ['_id' => 1, 'foo' => 'bar']),
            new CollectionTestModel((object) ['_id' => 2, 'bar' => 'baz']),
        ]);
    }
}

/** @phpcsIgnore PSR1.Classes.ClassDeclaration.MultipleClasses */
class CollectionTestModel
{
    public $data;
    public $marker;

    public function __construct(object $data, ?string $marker = null)
    {
        $this->data = $data;
    }
}
