<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToTimestamp;

/**
 * Returns the incrementing ordinal from a timestamp as a long.
 * New in version 5.1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/
 */
class TsIncrementAggregation implements ResolvesToLong
{
    public const NAME = '$tsIncrement';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Int64|ResolvesToTimestamp|int $expression */
    public Int64|ResolvesToTimestamp|int $expression;

    /**
     * @param Int64|ResolvesToTimestamp|int $expression
     */
    public function __construct(Int64|ResolvesToTimestamp|int $expression)
    {
        $this->expression = $expression;
    }
}
