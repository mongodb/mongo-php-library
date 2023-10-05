<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToLong;

/**
 * Converts value to a long.
 * New in version 4.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLong/
 */
class ToLongAggregation implements ResolvesToLong
{
    public const NAME = '$toLong';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param ExpressionInterface|mixed $expression */
    public mixed $expression;

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
