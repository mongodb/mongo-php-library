<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\WindowInterface;

/**
 * Returns an average of numerical values. Ignores non-numeric values.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/
 */
class AvgAccumulator implements AccumulatorInterface, WindowInterface
{
    public const NAME = '$avg';
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
