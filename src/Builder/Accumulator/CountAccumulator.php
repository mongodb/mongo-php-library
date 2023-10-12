<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns the number of documents in the group or window.
 * Distinct from the $count pipeline stage.
 * New in version 5.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count-accumulator/
 */
readonly class CountAccumulator implements AccumulatorInterface, WindowInterface
{
    public const NAME = '$count';
    public const ENCODE = Encode::Object;

    public function __construct()
    {
    }
}
