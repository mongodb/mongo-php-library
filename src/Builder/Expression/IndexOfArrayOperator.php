<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Searches an array for an occurrence of a specified value and returns the array index of the first occurrence. Array indexes start at zero.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfArray/
 * @internal
 */
final class IndexOfArrayOperator implements ResolvesToInt, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$indexOfArray';
    public const PROPERTIES = ['array' => 'array', 'search' => 'search', 'start' => 'start', 'end' => 'end'];

    /**
     * @var BSONArray|PackedArray|ResolvesToArray|array|string $array Can be any valid expression as long as it resolves to an array.
     * If the array expression resolves to a value of null or refers to a field that is missing, $indexOfArray returns null.
     * If the array expression does not resolve to an array or null nor refers to a missing field, $indexOfArray returns an error.
     */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $array;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $search */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $search;

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
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $array Can be any valid expression as long as it resolves to an array.
     * If the array expression resolves to a value of null or refers to a field that is missing, $indexOfArray returns null.
     * If the array expression does not resolve to an array or null nor refers to a missing field, $indexOfArray returns an error.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $search
     * @param Optional|ResolvesToInt|int|string $start An integer, or a number that can be represented as integers (such as 2.0), that specifies the starting index position for the search. Can be any valid expression that resolves to a non-negative integral number.
     * If unspecified, the starting index position for the search is the beginning of the string.
     * @param Optional|ResolvesToInt|int|string $end An integer, or a number that can be represented as integers (such as 2.0), that specifies the ending index position for the search. Can be any valid expression that resolves to a non-negative integral number. If you specify a <end> index value, you should also specify a <start> index value; otherwise, $indexOfArray uses the <end> value as the <start> index value instead of the <end> value.
     * If unspecified, the ending index position for the search is the end of the string.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $array,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $search,
        Optional|ResolvesToInt|int|string $start = Optional::Undefined,
        Optional|ResolvesToInt|int|string $end = Optional::Undefined,
    ) {
        if (is_string($array) && ! str_starts_with($array, '$')) {
            throw new InvalidArgumentException('Argument $array can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
        $this->search = $search;
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
