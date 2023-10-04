<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToInt;

class TopNAggregation implements AccumulatorInterface
{
    public const NAME = '$topN';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Int64|ResolvesToInt|int $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group. */
    public Int64|ResolvesToInt|int $n;

    /** @param array|object $sortBy Specifies the order of results, with syntax similar to $sort. */
    public array|object $sortBy;

    /** @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression. */
    public mixed $output;

    /**
     * @param Int64|ResolvesToInt|int $n limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param array|object $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|mixed $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(Int64|ResolvesToInt|int $n, array|object $sortBy, mixed $output)
    {
        $this->n = $n;
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
