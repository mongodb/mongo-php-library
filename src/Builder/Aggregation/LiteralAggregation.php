<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

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
