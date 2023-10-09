<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Optional;

/**
 * Outputs an array containing a sequence of integers according to user-defined inputs.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/range/
 */
class RangeAggregation implements ResolvesToArray
{
    public const NAME = '$range';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ResolvesToInt|int $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer. */
    public ResolvesToInt|int $start;

    /** @param ResolvesToInt|int $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer. */
    public ResolvesToInt|int $end;

    /** @param Optional|ResolvesToInt|int $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1. */
    public ResolvesToInt|Optional|int $step;

    /**
     * @param ResolvesToInt|int $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer.
     * @param ResolvesToInt|int $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer.
     * @param Optional|ResolvesToInt|int $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1.
     */
    public function __construct(
        ResolvesToInt|int $start,
        ResolvesToInt|int $end,
        ResolvesToInt|Optional|int $step = Optional::Undefined,
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->step = $step;
    }
}
