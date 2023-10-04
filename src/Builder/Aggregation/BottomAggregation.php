<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

class BottomAggregation implements AccumulatorInterface
{
    public const NAME = '$bottom';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param array|object $sortBy Specifies the order of results, with syntax similar to $sort. */
    public array|object $sortBy;

    /** @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression. */
    public mixed $output;

    /**
     * @param array|object $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(array|object $sortBy, mixed $output)
    {
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
