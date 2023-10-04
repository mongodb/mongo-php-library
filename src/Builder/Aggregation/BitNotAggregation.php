<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;

class BitNotAggregation implements ResolvesToInt, ResolvesToLong
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
