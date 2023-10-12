<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;

/**
 * Deprecated. Use $substrBytes or $substrCP.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/
 */
readonly class SubstrOperator implements ResolvesToString
{
    public const NAME = '$substr';
    public const ENCODE = Encode::Array;

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
