<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use stdClass;

class TopAggregation implements AccumulatorInterface
{
    public const NAME = '$top';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public stdClass|array $sortBy;

    /** @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression. */
    public mixed $output;

    /**
     * @param array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(stdClass|array $sortBy, mixed $output)
    {
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
