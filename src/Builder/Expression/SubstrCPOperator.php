<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Encode;

/**
 * Returns the substring of a string. Starts with the character at the specified UTF-8 code point (CP) index (zero-based) in the string and continues for the number of code points specified.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/
 */
class SubstrCPOperator implements ResolvesToString
{
    public const NAME = '$substrCP';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param ResolvesToString|non-empty-string $string */
    public ResolvesToString|string $string;

    /** @param ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "". */
    public ResolvesToInt|int $start;

    /** @param ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string. */
    public ResolvesToInt|int $length;

    /**
     * @param ResolvesToString|non-empty-string $string
     * @param ResolvesToInt|int $start If start is a negative number, $substr returns an empty string "".
     * @param ResolvesToInt|int $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public function __construct(ResolvesToString|string $string, ResolvesToInt|int $start, ResolvesToInt|int $length)
    {
        $this->string = $string;
        $this->start = $start;
        $this->length = $length;
    }
}
