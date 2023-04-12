<?php

namespace MongoDB\Tests\Exception;

use AssertionError;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;

class InvalidArgumentExceptionTest extends TestCase
{
    /** @dataProvider provideExpectedTypes */
    public function testExpectedTypeFormatting($expectedType, $typeString): void
    {
        $e = InvalidArgumentException::invalidType('$arg', null, $expectedType);
        $this->assertStringContainsString($typeString, $e->getMessage());
    }

    public function provideExpectedTypes()
    {
        yield 'expectedType is a string' => [
            'array',
            'type "array"',
        ];

        yield 'expectedType is an array with one string' => [
            ['array'],
            'type "array"',
        ];

        yield 'expectedType is an array with two strings' => [
            ['array', 'integer'],
            'type "array" or "integer"',
        ];

        yield 'expectedType is an array with three strings' => [
            ['array', 'integer', 'object'],
            'type "array", "integer", or "object"',
        ];
    }

    public function testExpectedTypeArrayMustNotBeEmpty(): void
    {
        $this->expectException(AssertionError::class);
        InvalidArgumentException::invalidType('$arg', null, []);
    }
}
