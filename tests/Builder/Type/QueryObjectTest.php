<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Type;

use Generator;
use LogicException;
use MongoDB\BSON;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query\CommentOperator;
use MongoDB\Builder\Query\EqOperator;
use MongoDB\Builder\Query\GtOperator;
use MongoDB\Builder\Query\LtOperator;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class QueryObjectTest extends TestCase
{
    public function testEmptyQueryObject(): void
    {
        $queryObject = QueryObject::create([]);

        $this->assertSame([], $queryObject->queries);
    }

    public function testEmptyArrayIsUnwrapped(): void
    {
        // An empty array given as a single argument must not become a "0" field
        $queryObject = QueryObject::create([[]]);

        $this->assertSame([], $queryObject->queries);
    }

    public function testEmptyArrayEncodesAsEmptyQuery(): void
    {
        $encoder = new BuilderEncoder();

        $this->assertEquals(
            [(object) ['$match' => new stdClass()]],
            $encoder->encode(new Pipeline(Stage::match([]))),
        );
        $this->assertEquals(
            new stdClass(),
            $encoder->encode(QueryObject::create([[]])),
        );
    }

    /** @param list<mixed> $queries */
    #[DataProvider('providePositionalQueries')]
    public function testPositionalQueriesAreMerged(array $queries, stdClass $expected): void
    {
        $encoder = new BuilderEncoder();

        $this->assertEquals($expected, $encoder->encode(QueryObject::create($queries)));
    }

    public static function providePositionalQueries(): Generator
    {
        yield 'empty object' => [[new stdClass()], new stdClass()];
        yield 'object' => [[(object) ['foo' => 1]], (object) ['foo' => 1]];
        yield 'multiple arrays' => [[['foo' => 1], ['bar' => 2]], (object) ['foo' => 1, 'bar' => 2]];
        yield 'multiple objects' => [[(object) ['foo' => 1], (object) ['bar' => 2]], (object) ['foo' => 1, 'bar' => 2]];
        yield 'array with query operator' => [[['$comment' => 'foo']], (object) ['$comment' => 'foo']];
        yield 'query operator and array' => [[new CommentOperator('foo'), ['bar' => 2]], (object) ['$comment' => 'foo', 'bar' => 2]];
        yield 'array and named argument' => [[['foo' => 1], 'bar' => 2], (object) ['foo' => 1, 'bar' => 2]];
    }

    public function testPositionalQueriesEncodeInStage(): void
    {
        $encoder = new BuilderEncoder();

        $this->assertEquals(
            [(object) ['$match' => new stdClass()]],
            $encoder->encode(new Pipeline(Stage::match(new stdClass()))),
        );
        $this->assertEquals(
            [(object) ['$match' => (object) ['foo' => 1, 'bar' => 2]]],
            $encoder->encode(new Pipeline(Stage::match(['foo' => 1], ['bar' => 2]))),
        );
    }

    public function testPositionalScalarIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected query to be a query operator, an array or a stdClass, int given');

        QueryObject::create([1]);
    }

    public function testDuplicateFieldInPositionalQueries(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Duplicate key "foo" in query object');

        (new BuilderEncoder())->encode(QueryObject::create([['foo' => 1], ['foo' => 2]]));
    }

    public function testShortCutQueryObject(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $queryObject = QueryObject::create([$query]);

        $this->assertSame($query, $queryObject);
    }

    /** @param array<array-key, mixed> $value */
    #[DataProvider('provideQueryObjectValue')]
    public function testCreateQueryObject(array $value, int $expectedCount = 1): void
    {
        $queryObject = QueryObject::create($value);

        $this->assertCount($expectedCount, $queryObject->queries);
    }

    /** @param array<array-key, mixed> $value */
    #[DataProvider('provideQueryObjectValue')]
    public function testCreateQueryObjectFromArray(array $value, int $expectedCount = 1): void
    {
        // $value is wrapped in an array as if the user used an array instead of variadic arguments
        $queryObject = QueryObject::create([$value]);

        $this->assertCount($expectedCount, $queryObject->queries);
    }

    public static function provideQueryObjectValue(): Generator
    {
        yield 'int' => [['foo' => 1]];
        yield 'float' => [['foo' => 1.1]];
        yield 'string' => [['foo' => 'bar']];
        yield 'bool' => [['foo' => true]];
        yield 'null' => [['foo' => null]];
        yield 'decimal128' => [['foo' => new BSON\Decimal128('1.1')]];
        yield 'int64' => [[1 => new BSON\Int64(1)]];
        yield 'objectId' => [['foo' => new BSON\ObjectId()]];
        yield 'binary' => [['foo' => new BSON\Binary('foo')]];
        yield 'regex' => [['foo' => new BSON\Regex('foo')]];
        yield 'datetime' => [['foo' => new BSON\UTCDateTime()]];
        yield 'timestamp' => [['foo' => new BSON\Timestamp(1234, 5678)]];
        yield 'bson document' => [['foo' => BSON\Document::fromPHP(['bar' => 'baz'])]];
        yield 'bson array' => [['foo' => BSON\PackedArray::fromPHP(['bar', 'baz'])]];
        yield 'object' => [['foo' => (object) ['bar' => 'baz']]];
        yield 'list' => [['foo' => ['bar', 'baz']]];
        yield 'operator as array' => [['foo' => ['$eq' => 1]]];
        yield 'operator as object' => [['foo' => (object) ['$eq' => 1]]];
        yield 'field query operator' => [['foo' => new EqOperator(1)]];
        yield 'query operator' => [[new CommentOperator('foo'), 'foo' => 1], 2];
        yield 'numeric field with array' => [[1 => [2, 3]]];
        yield 'numeric field with operator' => [[1 => ['$eq' => 2]]];
    }

    public function testFieldQueryList(): void
    {
        $queryObject = QueryObject::create(
            ['foo' => [new GtOperator(1), new LtOperator(5)]],
        );

        $this->assertArrayHasKey('foo', $queryObject->queries);
        $this->assertInstanceOf(CombinedFieldQuery::class, $queryObject->queries['foo']);
        $this->assertCount(2, $queryObject->queries['foo']->fieldQueries);
    }
}
