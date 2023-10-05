<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

class OrAggregation implements ResolvesToBool
{
    public const NAME = '$or';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /**
     * @no-named-arguments
     * @param list<ExpressionInterface|ResolvesToBool|bool|mixed> ...$expression
     */
    public array $expression;

    /**
     * @param ExpressionInterface|ResolvesToBool|bool|mixed $expression
     */
    public function __construct(mixed ...$expression)
    {
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of ExpressionInterface|ResolvesToBool|bool|mixed, named arguments are not supported');
        }
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        $this->expression = $expression;
    }
}
