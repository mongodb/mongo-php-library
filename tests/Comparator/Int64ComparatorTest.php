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

        yield 'Expects Int64, Actual int string' => [
            'expectedResult' => true,
            'expectedValue' => new Int64(123),
            'actualValue' => '123',
        ];

        yield 'Expects Int64, Actual float' => [
            'expectedResult' => true,
            'expectedValue' => new Int64(123),
            'actualValue' => 123.0,
        ];

        yield 'Expects Int64, Actual float string' => [
            'expectedResult' => true,
            'expectedValue' => new Int64(123),
            'actualValue' => '123.0',
        ];

        yield 'Expects Int64, Actual non-numeric string' => [
            'expectedResult' => false,
            'expectedValue' => new Int64(123),
            'actualValue' => 'foo',
        ];

        yield 'Expects int, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => 123,
            'actualValue' => new Int64(123),
        ];

        yield 'Expects int string, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => '123',
            'actualValue' => new Int64(123),
        ];

        yield 'Expects float, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => 123.0,
            'actualValue' => new Int64(123),
        ];

        yield 'Expects float string, Actual Int64' => [
            'expectedResult' => true,
            'expectedValue' => '123.0',
            'actualValue' => new Int64(123),
        ];

        yield 'Expects non-numeric string, Actual Int64' => [
            'expectedResult' => false,
            'expectedValue' => 'foo',
            'actualValue' => new Int64(123),
        ];

        yield 'Expects float, Actual float' => [
            'expectedResult' => false,
            'expectedValue' => 123.0,
            'actualValue' => 123.0,
        ];

        yield 'Expects numeric string, Actual numeric string' => [
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
            'expected' => new Int64(8_589_934_592),
            'actual' => new Int64(8_589_934_592),
        ];

        yield 'Expected Int64, Actual int' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => 8_589_934_592,
        ];

        yield 'Expected Int64, Actual int string' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => '8589934592',
        ];

        yield 'Expected Int64, Actual float' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => 8_589_934_592.0,
        ];

        yield 'Expected Int64, Actual float string' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => '8589934592.0',
        ];

        yield 'Expected int, Actual Int64' => [
            'expected' => 8_589_934_592,
            'actual' => new Int64(8_589_934_592),
        ];

        yield 'Expected int string, Actual Int64' => [
            'expected' => '8589934592',
            'actual' => new Int64(8_589_934_592),
        ];

        yield 'Expected float, Actual Int64' => [
            'expected' => 8_589_934_592.0,
            'actual' => new Int64(8_589_934_592),
        ];

        yield 'Expected float string, Actual Int64' => [
            'expected' => '8589934592.0',
            'actual' => new Int64(8_589_934_592),
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
            'expected' => new Int64(8_589_934_592),
            'actual' => new Int64(456),
        ];

        yield 'Expected Int64, Actual int' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => 456,
        ];

        yield 'Expected Int64, Actual int string' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => '456',
        ];

        yield 'Expected Int64, Actual float' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => 8_589_934_592.1,
        ];

        yield 'Expected Int64, Actual float string' => [
            'expected' => new Int64(8_589_934_592),
            'actual' => '8589934592.1',
        ];

        yield 'Expected int, Actual Int64' => [
            'expected' => 8_589_934_592,
            'actual' => new Int64(456),
        ];

        yield 'Expected int string, Actual Int64' => [
            'expected' => '8589934592',
            'actual' => new Int64(456),
        ];

        yield 'Expected float, Actual Int64' => [
            'expected' => 8_589_934_592.1,
            'actual' => new Int64(456),
        ];

        yield 'Expected float string, Actual Int64' => [
            'expected' => '8589934592.1',
            'actual' => new Int64(456),
        ];
    }
}
