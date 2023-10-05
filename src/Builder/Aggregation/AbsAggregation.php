<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Returns the absolute value of a number.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/abs/
 */
class AbsAggregation implements ResolvesToNumber
{
    public const NAME = '$abs';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $value */
    public Decimal128|Int64|ResolvesToNumber|float|int $value;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $value
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $value)
    {
        $this->value = $value;
    }
}
