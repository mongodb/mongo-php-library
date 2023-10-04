<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Optional;

class RangeAggregation implements ResolvesToArray
{
    public const NAME = '$range';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param Int64|ResolvesToInt|int $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer. */
    public Int64|ResolvesToInt|int $start;

    /** @param Int64|ResolvesToInt|int $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer. */
    public Int64|ResolvesToInt|int $end;

    /** @param Int64|Optional|ResolvesToInt|int $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1. */
    public Int64|ResolvesToInt|Optional|int $step;

    /**
     * @param Int64|ResolvesToInt|int $start An integer that specifies the start of the sequence. Can be any valid expression that resolves to an integer.
     * @param Int64|ResolvesToInt|int $end An integer that specifies the exclusive upper limit of the sequence. Can be any valid expression that resolves to an integer.
     * @param Int64|Optional|ResolvesToInt|int $step An integer that specifies the increment value. Can be any valid expression that resolves to a non-zero integer. Defaults to 1.
     */
    public function __construct(
        Int64|ResolvesToInt|int $start,
        Int64|ResolvesToInt|int $end,
        Int64|ResolvesToInt|Optional|int $step = Optional::Undefined,
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->step = $step;
    }
}
