<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;

/**
 * Returns the number of UTF-8 code points in a string.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenCP/
 */
class StrLenCPAggregation implements ResolvesToInt
{
    public const NAME = '$strLenCP';
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
