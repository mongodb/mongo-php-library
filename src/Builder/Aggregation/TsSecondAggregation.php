<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Timestamp;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToTimestamp;

/**
 * Returns the seconds from a timestamp as a long.
 * New in version 5.1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/
 */
class TsSecondAggregation implements ResolvesToLong
{
    public const NAME = '$tsSecond';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param ResolvesToTimestamp|Timestamp|int $expression */
    public Timestamp|ResolvesToTimestamp|int $expression;

    /**
     * @param ResolvesToTimestamp|Timestamp|int $expression
     */
    public function __construct(Timestamp|ResolvesToTimestamp|int $expression)
    {
        $this->expression = $expression;
    }
}
