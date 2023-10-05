<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToString;

/**
 * Converts a string to lowercase. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/
 */
class ToLowerAggregation implements ResolvesToString
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