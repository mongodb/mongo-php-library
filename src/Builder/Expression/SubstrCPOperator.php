<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Returns the substring of a string. Starts with the character at the specified UTF-8 code point (CP) index (zero-based) in the string and continues for the number of code points specified.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/
 * @internal
 */
final class SubstrCPOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$substrCP';
    public const PROPERTIES = ['string' => 'string', 'start' => 'start', 'length' => 'length'];

    /** @var ResolvesToString|string $string */
    public readonly ResolvesToString|string $string;

    /** @var ResolvesToInt|int|string $start If start is a negative number, $substr returns an empty string "". */
    public readonly ResolvesToInt|int|string $start;

    /** @var ResolvesToInt|int|string $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string. */
    public readonly ResolvesToInt|int|string $length;

    /**
     * @param ResolvesToString|string $string
     * @param ResolvesToInt|int|string $start If start is a negative number, $substr returns an empty string "".
     * @param ResolvesToInt|int|string $length If length is a negative number, $substr returns a substring that starts at the specified index and includes the rest of the string.
     */
    public function __construct(
        ResolvesToString|string $string,
        ResolvesToInt|int|string $start,
        ResolvesToInt|int|string $length,
    ) {
        $this->string = $string;
        $this->start = $start;
        $this->length = $length;
    }
}
