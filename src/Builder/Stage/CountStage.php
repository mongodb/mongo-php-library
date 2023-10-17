<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns a count of the number of documents at this stage of the aggregation pipeline.
 * Distinct from the $count aggregation accumulator.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/
 */
class CountStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var non-empty-string $field Name of the output field which has the count as its value. It must be a non-empty string, must not start with $ and must not contain the . character. */
    public readonly string $field;

    /**
     * @param non-empty-string $field Name of the output field which has the count as its value. It must be a non-empty string, must not start with $ and must not contain the . character.
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function getOperator(): string
    {
        return '$count';
    }
}
