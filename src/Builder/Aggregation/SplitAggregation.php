<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToString;

class SplitAggregation implements ResolvesToArray
{
    public const NAME = '$split';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ResolvesToString|non-empty-string $string The string to be split. string expression can be any valid expression as long as it resolves to a string. */
    public ResolvesToString|string $string;

    /** @param ResolvesToString|non-empty-string $delimiter The delimiter to use when splitting the string expression. delimiter can be any valid expression as long as it resolves to a string. */
    public ResolvesToString|string $delimiter;

    /**
     * @param ResolvesToString|non-empty-string $string The string to be split. string expression can be any valid expression as long as it resolves to a string.
     * @param ResolvesToString|non-empty-string $delimiter The delimiter to use when splitting the string expression. delimiter can be any valid expression as long as it resolves to a string.
     */
    public function __construct(ResolvesToString|string $string, ResolvesToString|string $delimiter)
    {
        $this->string = $string;
        $this->delimiter = $delimiter;
    }
}
