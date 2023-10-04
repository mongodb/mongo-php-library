<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\FieldPath;

class AddToSetAggregation implements AccumulatorInterface
{
    public const NAME = '$addToSet';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param ExpressionInterface|FieldPath|mixed|non-empty-string $expression */
    public mixed $expression;

    /**
     * @param ExpressionInterface|FieldPath|mixed|non-empty-string $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
