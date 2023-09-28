<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

class AndAggregation implements ResolvesToBool
{
    public const NAME = '$and';
    public const ENCODE = 'single';

    /** @param list<ExpressionInterface|mixed> ...$expressions */
    public array $expressions;

    /**
     * @param ExpressionInterface|mixed $expressions
     */
    public function __construct(mixed ...$expressions)
    {
        if (\count($expressions) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values, got %d.', 1, \count($expressions)));
        }

        $this->expressions = $expressions;
    }
}
