<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Timestamp;
use MongoDB\Builder\Encode;

/**
 * Returns the incrementing ordinal from a timestamp as a long.
 * New in version 5.1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/
 */
class TsIncrementOperator implements ResolvesToLong
{
    public const NAME = '$tsIncrement';
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
