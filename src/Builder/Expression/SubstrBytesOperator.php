<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Returns the substring of a string. Starts with the character at the specified UTF-8 byte index (zero-based) in the string and continues for the specified number of bytes.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/
 * @internal
 */
final class SubstrBytesOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$substrBytes';
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
        if (is_string($start) && ! str_starts_with($start, '$')) {
            throw new InvalidArgumentException('Argument $start can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->start = $start;
        if (is_string($length) && ! str_starts_with($length, '$')) {
            throw new InvalidArgumentException('Argument $length can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->length = $length;
    }
}
