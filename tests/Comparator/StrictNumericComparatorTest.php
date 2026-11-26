<?php

namespace MongoDB\Tests\Comparator;

use MongoDB\BSON\Int64;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

class StrictNumericComparatorTest extends TestCase
{
    #[DataProvider('provideFailingValues')]
    public function testFailingAssertions($expected, $actual): void
    {
        $this->expectException(ComparisonFailure::class);

        (new StrictNumericComparator())->assertEquals($expected, $actual);
    }

    /** @return iterable<string, array{int|float|numeric-string|Int64, int|float|numeric-string|Int64}> */
    public static function provideFailingValues(): iterable
    {
        yield 'String is never equal to an integer' => ['123', 123];
        yield 'String is never equal to a float' => ['123', 123.0];
        yield 'String is never equal to an Int64' => ['123', new Int64(123)];

        yield 'String is never equal to an integer (reverse)' => [123, '123'];
        yield 'String is never equal to a float (reverse)' => [123.0, '123'];
        yield 'String is never equal to an Int64 (reverse)' => [new Int64(123), '123'];

        yield 'Unequal Int64 are not equal' => [new Int64(123), new Int64(0)];
    }
}
