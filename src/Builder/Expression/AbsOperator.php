<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Returns the absolute value of a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/abs/
 */
class AbsOperator implements ResolvesToNumber
{
    public const NAME = '$abs';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $value */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $value;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $value
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $value)
    {
        $this->value = $value;
    }
}
