<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ExpressionInterface;

class SubtractAggregation implements ExpressionInterface
{
    public const NAME = '$subtract';
    public const ENCODE = 'array';

    public mixed $expression1;
    public mixed $expression2;

    /**
     * @param ExpressionInterface|mixed $expression1
     * @param ExpressionInterface|mixed $expression2
     */
    public function __construct(mixed $expression1, mixed $expression2)
    {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
