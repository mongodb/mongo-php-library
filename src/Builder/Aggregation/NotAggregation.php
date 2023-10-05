<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

/**
 * Returns the boolean value that is the opposite of its argument expression. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/
 */
class NotAggregation implements ResolvesToBool
{
    public const NAME = '$not';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param ExpressionInterface|ResolvesToBool|bool|mixed $expression */
    public mixed $expression;

    /**
     * @param ExpressionInterface|ResolvesToBool|bool|mixed $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
