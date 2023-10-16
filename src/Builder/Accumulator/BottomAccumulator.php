<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Returns the bottom element within a group according to the specified sort order.
 * New in version 5.2: Available in the $group and $setWindowFields stages.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/
 */
readonly class BottomAccumulator implements AccumulatorInterface, WindowInterface
{
    public const NAME = '$bottom';
    public const ENCODE = Encode::Object;

    /** @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public Document|Serializable|stdClass|array $sortBy;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression. */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /**
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(
        Document|Serializable|stdClass|array $sortBy,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
    ) {
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
