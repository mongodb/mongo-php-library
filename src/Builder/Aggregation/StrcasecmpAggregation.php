<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;

class StrcasecmpAggregation implements ResolvesToInt
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
