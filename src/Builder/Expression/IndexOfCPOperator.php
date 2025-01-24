<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Searches a string for an occurrence of a substring and returns the UTF-8 code point index of the first occurrence. If the substring is not found, returns -1
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfCP/
 * @internal
 */
final class IndexOfCPOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$indexOfCP';
    public const PROPERTIES = ['string' => 'string', 'substring' => 'substring', 'start' => 'start', 'end' => 'end'];

    /**
     * @var ResolvesToString|string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfCP returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfCP returns an error.
     */
    public readonly ResolvesToString|string $string;

    /** @var ResolvesToString|string $substring Can be any valid expression as long as it resolves to a string. */
    public readonly ResolvesToString|string $substring;

    /**
     * @var Optional|ResolvesToInt|int|string $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     */
    public readonly Optional|ResolvesToInt|int|string $start;

    /**
     * @var Optional|ResolvesToInt|int|string $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public readonly Optional|ResolvesToInt|int|string $end;

    /**
     * @param ResolvesToString|string $string Can be any valid expression as long as it resolves to a string.
     * If the string expression resolves to a value of null or refers to a field that is missing, $indexOfCP returns null.
     * If the string expression does not resolve to a string or null nor refers to a missing field, $indexOfCP returns an error.
     * @param ResolvesToString|string $substring Can be any valid expression as long as it resolves to a string.
     * @param Optional|ResolvesToInt|int|string $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int|string $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public function __construct(
        ResolvesToString|string $string,
        ResolvesToString|string $substring,
        Optional|ResolvesToInt|int|string $start = Optional::Undefined,
        Optional|ResolvesToInt|int|string $end = Optional::Undefined,
    ) {
        $this->string = $string;
        $this->substring = $substring;
        if (is_string($start) && ! str_starts_with($start, '$')) {
            throw new InvalidArgumentException('Argument $start can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->start = $start;
        if (is_string($end) && ! str_starts_with($end, '$')) {
            throw new InvalidArgumentException('Argument $end can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->end = $end;
    }
}
