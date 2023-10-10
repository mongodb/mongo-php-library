<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Encode;

/**
 * Converts a string to lowercase. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/
 */
class ToLowerOperator implements ResolvesToString
{
    public const NAME = '$toLower';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

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