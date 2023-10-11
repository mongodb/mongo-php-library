<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns a count of the number of documents at this stage of the aggregation pipeline.
 * Distinct from the $count aggregation accumulator.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/
 */
class CountStage implements StageInterface
{
    public const NAME = '$count';
    public const ENCODE = Encode::Single;

    /** @param non-empty-string $field */
    public string $field;

    /**
     * @param non-empty-string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }
}
