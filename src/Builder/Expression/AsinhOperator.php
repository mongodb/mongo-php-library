<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Returns the inverse hyperbolic sine (hyperbolic arc sine) of a value in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/
 */
class AsinhOperator implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$asinh';
    public const ENCODE = Encode::Single;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
