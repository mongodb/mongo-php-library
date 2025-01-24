<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use Generator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function is_subclass_of;
use function sprintf;

class FieldPathTest extends TestCase
{
    #[DataProvider('provideFieldPath')]
    public function testFieldPath(string $fieldPathClass, string $resolveClass): void
    {
        $fieldPath = Expression::{$fieldPathClass}('foo');
        $this->assertSame('foo', $fieldPath->name);
        $this->assertInstanceOf($resolveClass, $fieldPath);
        $this->assertInstanceOf(FieldPathInterface::class, $fieldPath);

        // Ensure FieldPath resolves to any type
        $this->assertTrue(is_subclass_of(Expression\FieldPath::class, $resolveClass), sprintf('%s instanceof %s', Expression\FieldPath::class, $resolveClass));
    }

    #[DataProvider('provideFieldPath')]
    public function testRejectDollarPrefix(string $fieldPathClass): void
    {
        $this->expectException(InvalidArgumentException::class);

        Expression::{$fieldPathClass}('$foo');
    }

    public static function provideFieldPath(): Generator
    {
        yield 'double' => ['doubleFieldPath', Expression\ResolvesToDouble::class];
        yield 'string' => ['stringFieldPath', Expression\ResolvesToString::class];
        yield 'object' => ['objectFieldPath', Expression\ResolvesToObject::class];
        yield 'array' => ['arrayFieldPath', Expression\ResolvesToArray::class];
        yield 'binData' => ['binDataFieldPath', Expression\ResolvesToBinData::class];
        yield 'objectId' => ['objectIdFieldPath', Expression\ResolvesToObjectId::class];
        yield 'bool' => ['boolFieldPath', Expression\ResolvesToBool::class];
        yield 'date' => ['dateFieldPath', Expression\ResolvesToDate::class];
        yield 'null' => ['nullFieldPath', Expression\ResolvesToNull::class];
        yield 'regex' => ['regexFieldPath', Expression\ResolvesToRegex::class];
        yield 'javascript' => ['javascriptFieldPath', Expression\ResolvesToJavascript::class];
        yield 'int' => ['intFieldPath', Expression\ResolvesToInt::class];
        yield 'timestamp' => ['timestampFieldPath', Expression\ResolvesToTimestamp::class];
        yield 'long' => ['longFieldPath', Expression\ResolvesToLong::class];
        yield 'decimal' => ['decimalFieldPath', Expression\ResolvesToDecimal::class];
        yield 'number' => ['numberFieldPath', Expression\ResolvesToNumber::class];
        yield 'any' => ['fieldPath', Expression\ResolvesToAny::class];
    }

    public function testStringFieldPathAcceptedAsExpression(): void
    {
        $operator = Expression::abs('$foo');

        $this->assertSame('$foo', $operator->value);
    }

    public function testNonDollarPrefixedStringRejected(): void
    {
        self::expectException(InvalidArgumentException::class);

        Expression::abs('foo');
    }
}
