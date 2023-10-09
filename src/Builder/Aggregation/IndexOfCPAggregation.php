<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;

/**
 * Searches a string for an occurrence of a substring and returns the UTF-8 code point index of the first occurrence. If the substring is not found, returns -1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfCP/
 */
class IndexOfCPAggregation implements ResolvesToInt
{
    public const NAME = '$indexOfCP';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /**
     * @param ResolvesToString|non-empty-string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfCP returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfCP returns an error.
     */
    public ResolvesToString|string $string;

    /** @param ResolvesToString|non-empty-string $substring Can be any valid expression as long as it resolves to a string. */
    public ResolvesToString|string $substring;

    /**
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     */
    public ResolvesToInt|Optional|int $start;

    /**
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public ResolvesToInt|Optional|int $end;

    /**
     * @param ResolvesToString|non-empty-string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfCP returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfCP returns an error.
     * @param ResolvesToString|non-empty-string $substring Can be any valid expression as long as it resolves to a string.
     * @param Optional|ResolvesToInt|int $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public function __construct(
        ResolvesToString|string $string,
        ResolvesToString|string $substring,
        ResolvesToInt|Optional|int $start = Optional::Undefined,
        ResolvesToInt|Optional|int $end = Optional::Undefined,
    ) {
        $this->string = $string;
        $this->substring = $substring;
        $this->start = $start;
        $this->end = $end;
    }
}
