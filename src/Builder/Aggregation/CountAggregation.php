<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\AccumulatorInterface;

/**
 * Returns the number of documents in the group or window.
 * Distinct from the $count pipeline stage.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/
 */
class CountAggregation implements AccumulatorInterface
{
    public const NAME = '$count';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

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
