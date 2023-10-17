<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Type;

use Generator;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CombinedFieldQueryTest extends TestCase
{
    public function testEmptyFieldQueries(): void
    {
        $fieldQueries = new CombinedFieldQuery([]);

        $this->assertSame([], $fieldQueries->fieldQueries);
    }

    public function testFieldQueries(): void
    {
        $fieldQueries = new CombinedFieldQuery([
            $this->createMock(CombinedFieldQuery::class),
            ['$gt' => 1],
            new CombinedFieldQuery([]),
        ]);

        $this->assertCount(3, $fieldQueries->fieldQueries);
    }

    /** @dataProvider provideInvalidFieldQuery */
    public function testRejectInvalidFieldQueries($invalidQuery): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CombinedFieldQuery([$invalidQuery]);
    }

    public static function provideInvalidFieldQuery(): Generator
    {
        yield 'int' => [1];
        yield 'float' => [1.1];
        yield 'string' => ['foo'];
        yield 'bool' => [true];
        yield 'null' => [null];
    }
}
