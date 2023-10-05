<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Returns the result of an expression for the last document in a group or window.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/
 */
class LastAggregation implements ExpressionInterface, AccumulatorInterface
{
    public const NAME = '$last';
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
