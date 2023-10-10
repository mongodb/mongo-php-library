<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Encode;

/**
 * Concatenates any number of strings.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concat/
 */
class ConcatOperator implements ResolvesToString
{
    public const NAME = '$concat';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param list<ResolvesToString|non-empty-string> ...$expression */
    public array $expression;

    /**
     * @param ResolvesToString|non-empty-string ...$expression
     * @no-named-arguments
     */
    public function __construct(ResolvesToString|string ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }
        if (! \array_is_list($expression)) {
            throw new \InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }
        $this->expression = $expression;
    }
}
