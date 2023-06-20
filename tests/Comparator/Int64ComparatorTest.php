<?php

namespace MongoDB\Tests\Comparator;

use Generator;
use MongoDB\BSON\Int64;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

class Int64ComparatorTest extends TestCase
{
    /** @dataProvider provideAcceptsValues */
    public function testAccepts(bool $expectedResult, $expectedValue, $actualValue): void
    {
        $this->assertSame($expectedResult, (new Int64Comparator())->accepts($expectedValue, $actualValue));
    }

    public static function provideAcceptsValues(): Generator
    {
        yield 'Expects Int64, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => new Int64(123),
            'actualValue' => new Int64(123),
        ];

        yield 'Expects Int64, Actual int' => [
            'expectedResult' => true,
            'expectedValue' => new Int64(123),
            'actualValue' => 123,
        ];

        yield 'Expects Int64, Actual string' => [
            'expectedResult' => true,
            'expectedValue' => new Int64(123),
            'actualValue' => '123',
        ];

        yield 'Expects Int64, Actual float' => [
            'expectedResult' => false,
            'expectedValue' => new Int64(123),
            'actualValue' => 123.0,
        ];

        yield 'Expects int, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => 123,
            'actualValue' => new Int64(123),
        ];

        yield 'Expects string, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => '123',
            'actualValue' => new Int64(123),
        ];

        yield 'Expects float, Actual Int64' => [
            'expectedResult' => false,
            'expectedValue' => 123.0,
            'actualValue' => new Int64(123),
        ];

        yield 'Expects float, Actual float' => [
            'expectedResult' => false,
            'expectedValue' => 123.0,
            'actualValue' => 123.0,
        ];

        yield 'Expects string, Actual string' => [
            'expectedResult' => false,
            'expectedValue' => '123',
            'actualValue' => '123',
        ];
    }

    /**
     * @dataProvider provideMatchingAssertions
     * @doesNotPerformAssertions
     */
    public function testMatchingAssertions($expected, $actual): void
    {
        (new Int64Comparator())->assertEquals($expected, $actual);
    }

    public static function provideMatchingAssertions(): Generator
    {
        yield 'Expected Int64, Actual Int64' => [
            'expected' => new Int64(123),
            'actual' => new Int64(123),
        ];

        yield 'Expected Int64, Actual int' => [
            'expected' => new Int64(123),
            'actual' => 123,
        ];

        yield 'Expected Int64, Actual string' => [
            'expected' => new Int64(123),
            'actual' => '123',
        ];

        yield 'Expected int, Actual Int64' => [
            'expected' => 123,
            'actual' => new Int64(123),
        ];

        yield 'Expected string, Actual Int64' => [
            'expected' => '123',
            'actual' => new Int64(123),
        ];
    }

    /** @dataProvider provideFailingValues */
    public function testFailingAssertions($expected, $actual): void
    {
        $this->expectException(ComparisonFailure::class);

        (new Int64Comparator())->assertEquals($expected, $actual);
    }

    public static function provideFailingValues(): Generator
    {
        yield 'Expected Int64, Actual Int64' => [
            'expected' => new Int64(123),
            'actual' => new Int64(456),
        ];

        yield 'Expected Int64, Actual int' => [
            'expected' => new Int64(123),
            'actual' => 456,
        ];

        yield 'Expected Int64, Actual string' => [
            'expected' => new Int64(123),
            'actual' => '456',
        ];

        yield 'Expected int, Actual Int64' => [
            'expected' => 123,
            'actual' => new Int64(456),
        ];

        yield 'Expected string, Actual Int64' => [
            'expected' => '123',
            'actual' => new Int64(456),
        ];
    }
}
