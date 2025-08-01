<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Regex;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;

/**
 * Splits a string into substrings based on a delimiter. Returns an array of substrings. If the delimiter is not found within the string, returns an array containing the original string.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/split/
 * @internal
 */
final class SplitOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$split';
    public const PROPERTIES = ['string' => 'string', 'delimiter' => 'delimiter'];

    /** @var ResolvesToString|string $string The string to be split. string expression can be any valid expression as long as it resolves to a string. */
    public readonly ResolvesToString|string $string;

    /** @var Regex|ResolvesToRegex|ResolvesToString|string $delimiter The delimiter to use when splitting the string expression. delimiter can be any valid expression as long as it resolves to a string. */
    public readonly Regex|ResolvesToRegex|ResolvesToString|string $delimiter;

    /**
     * @param ResolvesToString|string $string The string to be split. string expression can be any valid expression as long as it resolves to a string.
     * @param Regex|ResolvesToRegex|ResolvesToString|string $delimiter The delimiter to use when splitting the string expression. delimiter can be any valid expression as long as it resolves to a string.
     */
    public function __construct(
        ResolvesToString|string $string,
        Regex|ResolvesToRegex|ResolvesToString|string $delimiter,
    ) {
        $this->string = $string;
        $this->delimiter = $delimiter;
    }
}
