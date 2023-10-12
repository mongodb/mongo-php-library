<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Calculates the square root.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/
 */
readonly class SqrtOperator implements ResolvesToDouble
{
    public const NAME = '$sqrt';
    public const ENCODE = Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number. */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number The argument can be any valid expression as long as it resolves to a non-negative number.
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $number)
    {
        $this->number = $number;
    }
}
