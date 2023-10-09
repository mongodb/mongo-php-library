<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Calculates the square root.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/
 */
class SqrtAggregation implements ResolvesToDouble
{
    public const NAME = '$sqrt';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number. */
    public Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number;

    /**
     * @param Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int $number,
    ) {
        $this->number = $number;
    }
}
