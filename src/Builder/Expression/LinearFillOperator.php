<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;

/**
 * Fills null and missing fields in a window using linear interpolation based on surrounding field values.
 * Available in the $setWindowFields stage.
 * New in version 5.3.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/
 */
readonly class LinearFillOperator implements ResolvesToNumber
{
    public const NAME = '$linearFill';
    public const ENCODE = Encode::Single;

    /** @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression */
    public Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression;

    /**
     * @param Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression
     */
    public function __construct(Decimal128|Int64|ResolvesToInt|ResolvesToNumber|float|int $expression)
    {
        $this->expression = $expression;
    }
}
