<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Type;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Query\CommentOperator;
use MongoDB\Builder\Query\EqOperator;
use MongoDB\Builder\Query\GtOperator;
use MongoDB\Builder\Query\LtOperator;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use PHPUnit\Framework\TestCase;

use function array_keys;

class QueryObjectTest extends TestCase
{
    public function testEmptyQueryObject(): void
    {
        $queryObject = QueryObject::create();

        $this->assertSame([], $queryObject->queries);
    }

    public function testShortCutQueryObject(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $queryObject = QueryObject::create($query);

        $this->assertSame($query, $queryObject);
    }

    /**
     * @param array<array-key, mixed> $value
     *
     * @dataProvider provideQueryObjectValue
     */
    public function testCreateQueryObject(array $value, int $expectedCount = 1): void
    {
        $queryObject = QueryObject::create(...$value);

        $this->assertCount($expectedCount, $queryObject->queries);
    }

    public function provideQueryObjectValue(): array
    {
        return [
            'int' => [['foo' => 1]],
            'float' => [['foo' => 1.1]],
            'string' => [['foo' => 'bar']],
            'bool' => [['foo' => true]],
            'null' => [['foo' => null]],
            'regex' => [['foo' => new Regex('foo')]],
            'object' => [['foo' => (object) ['bar' => 'baz']]],
            'list' => [['foo' => ['bar', 'baz']]],
            'operator as array' => [['foo' => ['$eq' => 1]]],
            'operator as object' => [['foo' => (object) ['$eq' => 1]]],
            'field query operator' => [['foo' => new EqOperator(1)]],
            'query operator' => [[new CommentOperator('foo'), 'foo' => 1], 2],
        ];
    }

    public function testFieldQueryList(): void
    {
        $queryObject = QueryObject::create(
            foo: [new GtOperator(1), new LtOperator(5)],
        );

        $this->assertArrayHasKey('foo', $queryObject->queries);
        $this->assertInstanceOf(CombinedFieldQuery::class, $queryObject->queries['foo']);
        $this->assertCount(2, $queryObject->queries['foo']->fieldQueries);
    }
}
