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
 * Returns the hyperbolic sine of a value that is measured in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sinh/
 */
class SinhAggregation implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$sinh';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression $sinh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $sinh returns values as a double. $sinh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression $sinh takes any valid expression that resolves to a number, measured in radians. If the expression returns a value in degrees, use the $degreesToRadians operator to convert the value to radians.
     * By default $sinh returns values as a double. $sinh can also return values as a 128-bit decimal if the expression resolves to a 128-bit decimal value.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $expression,
    ) {
        $this->expression = $expression;
    }
}
