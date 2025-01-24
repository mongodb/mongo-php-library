<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Converts a value from degrees to radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/degreesToRadians/
 * @internal
 */
final class DegreesToRadiansOperator implements ResolvesToDouble, ResolvesToDecimal, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$degreesToRadians';
    public const PROPERTIES = ['expression' => 'expression'];

    /**
     * @var Decimal128|Int64|ResolvesToNumber|float|int|string $expression $degreesToRadians takes any valid expression that resolves to a number.
     * By default $degreesToRadians returns values as a double. $degreesToRadians can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $expression $degreesToRadians takes any valid expression that resolves to a number.
     * By default $degreesToRadians returns values as a double. $degreesToRadians can also return values as a 128-bit decimal as long as the <expression> resolves to a 128-bit decimal value.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $expression)
    {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->expression = $expression;
    }
}
