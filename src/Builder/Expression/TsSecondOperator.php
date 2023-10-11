<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Timestamp;
use MongoDB\Builder\Type\Encode;

/**
 * Returns the seconds from a timestamp as a long.
 * New in version 5.1.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/
 */
class TsSecondOperator implements ResolvesToLong
{
    public const NAME = '$tsSecond';
    public const ENCODE = Encode::Single;

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
