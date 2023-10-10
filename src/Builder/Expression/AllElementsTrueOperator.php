<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;

/**
 * Returns true if no element of a set evaluates to false, otherwise, returns false. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/allElementsTrue/
 */
class AllElementsTrueOperator implements ResolvesToBool
{
    public const NAME = '$allElementsTrue';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param list<BSONArray|PackedArray|ResolvesToArray|array> ...$expression */
    public array $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array ...$expression
     * @no-named-arguments
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
