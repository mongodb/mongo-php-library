<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Type;

use Generator;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\TimeUnit;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OutputWindowTest extends TestCase
{
    public function testWithoutWindowOptions(): void
    {
        $outputWindow = new OutputWindow(
            operator: $operator = $this->createMock(WindowInterface::class),
        );

        $this->assertSame($operator, $outputWindow->operator);
        $this->assertSame(Optional::Undefined, $outputWindow->window);
    }

    public function testWithDocuments(): void
    {
        $outputWindow = new OutputWindow(
            operator: $operator = $this->createMock(WindowInterface::class),
            documents: [1, 5],
        );

        $this->assertSame($operator, $outputWindow->operator);
        $this->assertEquals((object) ['documents' => [1, 5]], $outputWindow->window);
    }

    public function testWithRange(): void
    {
        $outputWindow = new OutputWindow(
            operator: $operator = $this->createMock(WindowInterface::class),
            range: [1.2, 5.8],
        );

        $this->assertSame($operator, $outputWindow->operator);
        $this->assertEquals((object) ['range' => [1.2, 5.8]], $outputWindow->window);
    }

    public function testWithUnit(): void
    {
        $outputWindow = new OutputWindow(
            operator: $operator = $this->createMock(WindowInterface::class),
            unit: TimeUnit::Day,
        );

        $this->assertSame($operator, $outputWindow->operator);
        $this->assertEquals((object) ['unit' => TimeUnit::Day], $outputWindow->window);
    }

    /** @param array<mixed> $documents */
    #[DataProvider('provideInvalidDocuments')]
    public function testRejectInvalidDocuments(array $documents): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected $documents argument to be a list of 2 string or int');

        new OutputWindow(
            operator: $this->createMock(WindowInterface::class),
            documents: $documents,
        );
    }

    public static function provideInvalidDocuments(): Generator
    {
        yield 'too few' => [[1]];
        yield 'too many' => [[1, 2, 3]];
        yield 'invalid boolean' => [[1, true]];
        yield 'invalid float' => [[1, 4.3]];
        yield 'not a list' => [['foo' => 1, 'bar' => 2]];
    }

    /** @param array<mixed> $range */
    #[DataProvider('provideInvalidRange')]
    public function testRejectInvalidRange(array $range): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected $range argument to be a list of 2 string or numeric');

        new OutputWindow(
            operator: $this->createMock(WindowInterface::class),
            range: $range,
        );
    }

    public static function provideInvalidRange(): Generator
    {
        yield 'too few' => [[1]];
        yield 'too many' => [[1, 2, 3]];
        yield 'invalid boolean' => [[1, true]];
        yield 'not a list' => [['foo' => 1, 'bar' => 2]];
    }
}
