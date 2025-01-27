<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Selects a subset of the array to return an array with only the elements that match the filter condition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/
 * @internal
 */
final class FilterOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$filter';
    public const PROPERTIES = ['input' => 'input', 'cond' => 'cond', 'as' => 'as', 'limit' => 'limit'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $input */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /** @var ResolvesToBool|bool|string $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as. */
    public readonly ResolvesToBool|bool|string $cond;

    /** @var Optional|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this. */
    public readonly Optional|string $as;

    /**
     * @var Optional|ResolvesToInt|int|string $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public readonly Optional|ResolvesToInt|int|string $limit;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input
     * @param ResolvesToBool|bool|string $cond An expression that resolves to a boolean value used to determine if an element should be included in the output array. The expression references each element of the input array individually with the variable name specified in as.
     * @param Optional|string $as A name for the variable that represents each individual element of the input array. If no name is specified, the variable name defaults to this.
     * @param Optional|ResolvesToInt|int|string $limit A number expression that restricts the number of matching array elements that $filter returns. You cannot specify a limit less than 1. The matching array elements are returned in the order they appear in the input array.
     * If the specified limit is greater than the number of matching array elements, $filter returns all matching array elements. If the limit is null, $filter returns all matching array elements.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
        ResolvesToBool|bool|string $cond,
        Optional|string $as = Optional::Undefined,
        Optional|ResolvesToInt|int|string $limit = Optional::Undefined,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        if (is_string($cond) && ! str_starts_with($cond, '$')) {
            throw new InvalidArgumentException('Argument $cond can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->cond = $cond;
        $this->as = $as;
        if (is_string($limit) && ! str_starts_with($limit, '$')) {
            throw new InvalidArgumentException('Argument $limit can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->limit = $limit;
    }
}
