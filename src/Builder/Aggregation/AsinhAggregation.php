<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Returns the inverse hyperbolic sine (hyperbolic arc sine) of a value in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/
 */
class AsinhAggregation implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$asinh';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression $asinh takes any valid expression that resolves to a number.
     * $asinh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $asinh returns values as a double. $asinh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression,
    ) {
        $this->expression = $expression;
    }
}
