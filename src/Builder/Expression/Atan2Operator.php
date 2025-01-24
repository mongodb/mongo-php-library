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
 * Returns the inverse tangent (arc tangent) of y / x in radians, where y and x are the first and second values passed to the expression respectively.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan2/
 * @internal
 */
final class Atan2Operator implements ResolvesToDouble, ResolvesToDecimal, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$atan2';
    public const PROPERTIES = ['y' => 'y', 'x' => 'x'];

    /**
     * @var Decimal128|Int64|ResolvesToNumber|float|int|string $y $atan2 takes any valid expression that resolves to a number.
     * $atan2 returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atan returns values as a double. $atan2 can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $y;

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $x */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $x;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $y $atan2 takes any valid expression that resolves to a number.
     * $atan2 returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $atan returns values as a double. $atan2 can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $x
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $y,
        Decimal128|Int64|ResolvesToNumber|float|int|string $x,
    ) {
        if (is_string($y) && ! str_starts_with($y, '$')) {
            throw new InvalidArgumentException('Argument $y can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->y = $y;
        if (is_string($x) && ! str_starts_with($x, '$')) {
            throw new InvalidArgumentException('Argument $x can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->x = $x;
    }
}
