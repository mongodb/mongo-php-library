<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Returns the smallest integer greater than or equal to the specified number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ceil/
 */
class CeilOperator implements ResolvesToInt
{
    public const NAME = '$ceil';
    public const ENCODE = Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN. */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression If the argument resolves to a value of null or refers to a field that is missing, $ceil returns null. If the argument resolves to NaN, $ceil returns NaN.
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
