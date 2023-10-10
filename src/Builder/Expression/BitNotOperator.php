<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Returns the result of a bitwise not operation on a single argument or an array that contains a single int or long value.
 * New in version 6.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitNot/
 */
class BitNotOperator implements ResolvesToInt, ResolvesToLong
{
    public const NAME = '$bitNot';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Int64|ResolvesToInt|ResolvesToLong|int $expression */
    public Int64|ResolvesToInt|ResolvesToLong|int $expression;

    /**
     * @param Int64|ResolvesToInt|ResolvesToLong|int $expression
     */
    public function __construct(Int64|ResolvesToInt|ResolvesToLong|int $expression)
    {
        $this->expression = $expression;
    }
}
