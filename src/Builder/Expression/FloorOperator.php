<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Returns the largest integer less than or equal to the specified number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/floor/
 */
class FloorOperator implements ResolvesToInt
{
    public const NAME = '$floor';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
