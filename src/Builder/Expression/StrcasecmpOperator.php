<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Encode;

/**
 * Performs case-insensitive string comparison and returns: 0 if two strings are equivalent, 1 if the first string is greater than the second, and -1 if the first string is less than the second.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strcasecmp/
 */
class StrcasecmpOperator implements ResolvesToInt
{
    public const NAME = '$strcasecmp';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ResolvesToString|non-empty-string $expression1 */
    public ResolvesToString|string $expression1;

    /** @param ResolvesToString|non-empty-string $expression2 */
    public ResolvesToString|string $expression2;

    /**
     * @param ResolvesToString|non-empty-string $expression1
     * @param ResolvesToString|non-empty-string $expression2
     */
    public function __construct(ResolvesToString|string $expression1, ResolvesToString|string $expression2)
    {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
