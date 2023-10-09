<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToDate;
use MongoDB\Builder\Expression\ResolvesToDecimal;
use MongoDB\Builder\Expression\ResolvesToDouble;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToLong;
use MongoDB\Builder\Expression\ResolvesToNumber;

/**
 * Adds numbers to return the sum, or adds numbers and a date to return a new date. If adding numbers and a date, treats the numbers as milliseconds. Accepts any number of argument expressions, but at most, one expression can resolve to a date.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/
 */
class AddAggregation implements ResolvesToNumber, ResolvesToDate
{
    public const NAME = '$add';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param list<Decimal128|Int64|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|UTCDateTime|float|int> ...$expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date. */
    public array $expression;

    /**
     * @param Decimal128|Int64|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|UTCDateTime|float|int ...$expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date.
     * @no-named-arguments
     */
    public function __construct(
        Decimal128|Int64|UTCDateTime|ResolvesToDate|ResolvesToDecimal|ResolvesToDouble|ResolvesToInt|ResolvesToLong|ResolvesToNumber|float|int ...$expression,
    ) {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
