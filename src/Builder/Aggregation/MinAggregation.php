<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ExpressionInterface;

class MinAggregation implements ExpressionInterface
{
    public const NAME = '$min';
    public const ENCODE = 'single';

    public mixed $expression;

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
