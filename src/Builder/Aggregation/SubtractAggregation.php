<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use DateTimeInterface;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToNumber;

class SubtractAggregation implements ResolvesToNumber, ResolvesToDate
{
    public const NAME = '$subtract';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1 */
    public \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $expression1;

    /** @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2 */
    public \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $expression2;

    /**
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression1
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression2
     */
    public function __construct(
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $expression1,
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
