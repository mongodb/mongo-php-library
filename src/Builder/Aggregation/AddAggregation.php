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

class AddAggregation implements ResolvesToNumber, ResolvesToDate
{
    public const NAME = '$add';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /**
     * @no-named-arguments
     * @param list<DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int> ...$expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date.
     */
    public array $expression;

    /**
     * @param DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int $expression The arguments can be any valid expression as long as they resolve to either all numbers or to numbers and a date.
     */
    public function __construct(
        \UTCDateTime|DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|float|int ...$expression,
    ) {
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of DateTimeInterface|Decimal128|Int64|ResolvesToDate|ResolvesToNumber|UTCDateTime|float|int, named arguments are not supported');
        }
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        $this->expression = $expression;
    }
}
