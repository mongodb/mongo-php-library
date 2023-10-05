<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Model\BSONArray;

/**
 * Returns a set with elements that appear in all of the input sets. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/
 */
class SetIntersectionAggregation implements ResolvesToArray
{
    public const NAME = '$setIntersection';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed>> ...$expression */
    public array $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed> ...$expression
     * @no-named-arguments
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list of BSONArray|PackedArray|ResolvesToArray|list<ExpressionInterface|mixed>, named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
