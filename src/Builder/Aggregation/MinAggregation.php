<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Returns the minimum value that results from applying an expression to each document.
 * Changed in version 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/
 */
class MinAggregation implements ExpressionInterface, AccumulatorInterface
{
    public const NAME = '$min';
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