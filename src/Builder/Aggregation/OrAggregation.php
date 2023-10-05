<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

/**
 * Returns true when any of its expressions evaluates to true. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/or/
 */
class OrAggregation implements ResolvesToBool
{
    public const NAME = '$or';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<ExpressionInterface|ResolvesToBool|bool|mixed> ...$expression */
    public array $expression;

    /**
     * @param ExpressionInterface|ResolvesToBool|bool|mixed ...$expression
     * @no-named-arguments
     */
    public function __construct(mixed ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of ExpressionInterface|ResolvesToBool|bool|mixed, named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
