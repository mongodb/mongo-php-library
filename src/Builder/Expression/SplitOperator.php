<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;

/**
 * Splits a string into substrings based on a delimiter. Returns an array of substrings. If the delimiter is not found within the string, returns an array containing the original string.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/split/
 */
readonly class SplitOperator implements ResolvesToArray
{
    public const NAME = '$split';
    public const ENCODE = Encode::Array;

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
