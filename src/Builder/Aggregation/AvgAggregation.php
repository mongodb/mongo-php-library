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
 * Returns an average of numerical values. Ignores non-numeric values.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/
 */
class AvgAggregation implements ResolvesToNumber, AccumulatorInterface
{
    public const NAME = '$avg';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<Decimal128|Int64|ResolvesToNumber|float|int> ...$expression */
    public array $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int ...$expression
     * @no-named-arguments
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of Decimal128|Int64|ResolvesToNumber|float|int, named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
