<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Return a value without parsing. Use for values that the aggregation pipeline may interpret as an expression. For example, use a $literal expression to a string that starts with a dollar sign ($) to avoid parsing as a field path.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/literal/
 */
class LiteralAggregation implements ExpressionInterface
{
    public const NAME = '$literal';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param mixed $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression. */
    public mixed $value;

    /**
     * @param mixed $value If the value is an expression, $literal does not evaluate the expression but instead returns the unparsed expression.
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }
}
