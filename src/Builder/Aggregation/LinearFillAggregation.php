<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Fills null and missing fields in a window using linear interpolation based on surrounding field values.
 * Available in the $setWindowFields stage.
 * New in version 5.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/
 */
class LinearFillAggregation implements ResolvesToNumber
{
    public const NAME = '$linearFill';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Decimal128|Int64|ResolvesToNumber|float|int $expression */
    public Decimal128|Int64|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int $expression
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
