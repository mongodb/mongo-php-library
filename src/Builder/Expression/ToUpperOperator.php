<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;

/**
 * Converts a string to uppercase. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toUpper/
 */
readonly class ToUpperOperator implements ResolvesToString
{
    public const NAME = '$toUpper';
    public const ENCODE = Encode::Single;

    /** @param ResolvesToString|non-empty-string $expression */
    public ResolvesToString|string $expression;

    /**
     * @param ResolvesToString|non-empty-string $expression
     */
    public function __construct(ResolvesToString|string $expression)
    {
        $this->expression = $expression;
    }
}
