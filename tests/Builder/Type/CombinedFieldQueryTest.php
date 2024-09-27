<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Type;

use Generator;
use MongoDB\Builder\Query\EqOperator;
use MongoDB\Builder\Query\GtOperator;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CombinedFieldQueryTest extends TestCase
{
    public function testEmpty(): void
    {
        $fieldQueries = new CombinedFieldQuery([]);

        $this->assertSame([], $fieldQueries->fieldQueries);
    }

    public function testSupportedTypes(): void
    {
        $fieldQueries = new CombinedFieldQuery([
            new EqOperator(1),
            ['$gt' => 1],
            (object) ['$lt' => 1],
        ]);

        $this->assertCount(3, $fieldQueries->fieldQueries);
    }

    public function testFlattenCombinedFieldQueries(): void
    {
        $fieldQueries = new CombinedFieldQuery([
            new CombinedFieldQuery([
                new CombinedFieldQuery([
                    ['$lt' => 1],
                    new CombinedFieldQuery([]),
                ]),
                ['$gt' => 1],
            ]),
            ['$gte' => 1],
        ]);

        $this->assertCount(3, $fieldQueries->fieldQueries);
    }

    #[DataProvider('provideInvalidFieldQuery')]
    public function testRejectInvalidFieldQueries(mixed $invalidQuery, string $message = '-'): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        new CombinedFieldQuery([$invalidQuery]);
    }

    public static function provideInvalidFieldQuery(): Generator
    {
        yield 'int' => [1, 'Expected filters to be a list of field query operators, array or stdClass, int given'];
        yield 'float' => [1.1, 'Expected filters to be a list of field query operators, array or stdClass, float given'];
        yield 'string' => ['foo', 'Expected filters to be a list of field query operators, array or stdClass, string given'];
        yield 'bool' => [true, 'Expected filters to be a list of field query operators, array or stdClass, bool given'];
        yield 'null' => [null, 'Expected filters to be a list of field query operators, array or stdClass, null given'];
        yield 'empty array' => [[], 'Operator must contain exactly one key, 0 given'];
        yield 'array with two keys' => [['$eq' => 1, '$ne' => 2], 'Operator must contain exactly one key, 2 given'];
        yield 'array key without $' => [['eq' => 1], 'Operator must contain exactly one key starting with $, "eq" given'];
        yield 'empty object' => [(object) [], 'Operator must contain exactly one key, 0 given'];
        yield 'object with two keys' => [(object) ['$eq' => 1, '$ne' => 2], 'Operator must contain exactly one key, 2 given'];
        yield 'object key without $' => [(object) ['eq' => 1], 'Operator must contain exactly one key starting with $, "eq" given'];
    }

    /** @param array<mixed> $fieldQueries */
    #[DataProvider('provideDuplicateOperator')]
    public function testRejectDuplicateOperator(array $fieldQueries): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Duplicate operator "$eq" detected');

        new CombinedFieldQuery([
            ['$eq' => 1],
            new EqOperator(2),
        ]);
    }

    public static function provideDuplicateOperator(): Generator
    {
        yield 'array and FieldQuery' => [
            [
                ['$eq' => 1],
                new EqOperator(2),
            ],
        ];

        yield 'object and FieldQuery' => [
            [
                (object) ['$gt' => 1],
                new GtOperator(2),
            ],
        ];

        yield 'object and array' => [
            [
                (object) ['$ne' => 1],
                ['$ne' => 2],
            ],
        ];
    }
}
