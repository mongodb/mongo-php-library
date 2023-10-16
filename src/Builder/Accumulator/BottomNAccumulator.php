<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Returns an aggregation of the bottom n elements within a group, according to the specified sort order. If the group contains fewer than n elements, $bottomN returns all elements in the group.
 * New in version 5.2.
 * Available in the $group and $setWindowFields stages.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/
 */
readonly class BottomNAccumulator implements AccumulatorInterface, WindowInterface
{
    public const NAME = '$bottomN';
    public const ENCODE = Encode::Object;

    /** @param ResolvesToInt|int $n Limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group. */
    public ResolvesToInt|int $n;

    /** @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public Document|Serializable|stdClass|array $sortBy;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression. */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /**
     * @param ResolvesToInt|int $n Limits the number of results per group and has to be a positive integral expression that is either a constant or depends on the _id value for $group.
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(
        ResolvesToInt|int $n,
        Document|Serializable|stdClass|array $sortBy,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
    ) {
        $this->n = $n;
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
