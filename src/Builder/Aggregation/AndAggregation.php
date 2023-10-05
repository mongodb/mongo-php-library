<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;
use MongoDB\Builder\Expression\ResolvesToNull;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Expression\ResolvesToString;

class AndAggregation implements ResolvesToBool
{
    public const NAME = '$and';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @no-named-arguments
     * @param list<Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|bool|float|int|mixed|non-empty-string|null> ...$expression
     */
    public array $expression;

    /**
     * @param Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|bool|float|int|mixed|non-empty-string|null $expression
     */
    public function __construct(mixed ...$expression)
    {
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of Decimal128|ExpressionInterface|Int64|ResolvesToBool|ResolvesToNull|ResolvesToNumber|ResolvesToString|bool|float|int|mixed|non-empty-string|null, named arguments are not supported');
        }
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        $this->expression = $expression;
    }
}
