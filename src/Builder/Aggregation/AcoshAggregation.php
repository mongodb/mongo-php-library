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
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Returns the inverse hyperbolic cosine (hyperbolic arc cosine) of a value in radians.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acosh/
 */
class AcoshAggregation implements ResolvesToDouble, ResolvesToDecimal
{
    public const NAME = '$acosh';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acosh takes any valid expression that resolves to a number between 1 and +Infinity, e.g. 1 <= value <= +Infinity.
     * $acosh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acosh returns values as a double. $acosh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public Decimal128|Int64|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression $acosh takes any valid expression that resolves to a number between 1 and +Infinity, e.g. 1 <= value <= +Infinity.
     * $acosh returns values in radians. Use $radiansToDegrees operator to convert the output value from radians to degrees.
     * By default $acosh returns values as a double. $acosh can also return values as a 128-bit decimal as long as the expression resolves to a 128-bit decimal value.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
