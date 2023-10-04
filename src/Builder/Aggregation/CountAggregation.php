<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;

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
