<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Returns the bottom element within a group according to the specified sort order.
 * New in MongoDB 5.2: Available in the $group and $setWindowFields stages.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/
 * @internal
 */
final class BottomAccumulator implements AccumulatorInterface, WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$bottom';
    public const PROPERTIES = ['sortBy' => 'sortBy', 'output' => 'output'];

    /** @var Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort. */
    public readonly Document|Serializable|stdClass|array $sortBy;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Represents the output for each element in the group and can be any expression. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output;

    /**
     * @param Document|Serializable|array|stdClass $sortBy Specifies the order of results, with syntax similar to $sort.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $output Represents the output for each element in the group and can be any expression.
     */
    public function __construct(
        Document|Serializable|stdClass|array $sortBy,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $output,
    ) {
        $this->sortBy = $sortBy;
        $this->output = $output;
    }
}
