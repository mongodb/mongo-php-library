<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

/**
 * Returns boolean true if the specified expression resolves to an integer, decimal, double, or long.
 * Returns boolean false if the expression resolves to any other BSON type, null, or a missing field.
 * New in version 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/
 */
class IsNumberAggregation implements ResolvesToBool
{
    public const NAME = '$isNumber';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<ExpressionInterface|mixed> ...$expression */
    public array $expression;

    /**
     * @param ExpressionInterface|mixed ...$expression
     * @no-named-arguments
     */
    public function __construct(mixed ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of ExpressionInterface|mixed, named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
