<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Expression\ResolvesToInt;

class SumAggregation implements ResolvesToInt
{
    public const NAME = '$sum';
    public const ENCODE = 'single';

    public mixed $expression;

    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
