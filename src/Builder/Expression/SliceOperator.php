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
 * Returns a subset of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/
 * @internal
 */
final class SliceOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$slice';
    public const PROPERTIES = ['expression' => 'expression', 'n' => 'n', 'position' => 'position'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $expression Any valid expression as long as it resolves to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $expression;

    /**
     * @var ResolvesToInt|int|string $n Any valid expression as long as it resolves to an integer. If position is specified, n must resolve to a positive integer.
     * If positive, $slice returns up to the first n elements in the array. If the position is specified, $slice returns the first n elements starting from the position.
     * If negative, $slice returns up to the last n elements in the array. n cannot resolve to a negative number if <position> is specified.
     */
    public readonly ResolvesToInt|int|string $n;

    /**
     * @var Optional|ResolvesToInt|int|string $position Any valid expression as long as it resolves to an integer.
     * If positive, $slice determines the starting position from the start of the array. If position is greater than the number of elements, the $slice returns an empty array.
     * If negative, $slice determines the starting position from the end of the array. If the absolute value of the <position> is greater than the number of elements, the starting position is the start of the array.
     */
    public readonly Optional|ResolvesToInt|int|string $position;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $expression Any valid expression as long as it resolves to an array.
     * @param ResolvesToInt|int|string $n Any valid expression as long as it resolves to an integer. If position is specified, n must resolve to a positive integer.
     * If positive, $slice returns up to the first n elements in the array. If the position is specified, $slice returns the first n elements starting from the position.
     * If negative, $slice returns up to the last n elements in the array. n cannot resolve to a negative number if <position> is specified.
     * @param Optional|ResolvesToInt|int|string $position Any valid expression as long as it resolves to an integer.
     * If positive, $slice determines the starting position from the start of the array. If position is greater than the number of elements, the $slice returns an empty array.
     * If negative, $slice determines the starting position from the end of the array. If the absolute value of the <position> is greater than the number of elements, the starting position is the start of the array.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $expression,
        ResolvesToInt|int|string $n,
        Optional|ResolvesToInt|int|string $position = Optional::Undefined,
    ) {
        if (is_string($expression) && ! str_starts_with($expression, '$')) {
            throw new InvalidArgumentException('Argument $expression can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($expression) && ! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression argument to be a list, got an associative array.');
        }

        $this->expression = $expression;
        if (is_string($n) && ! str_starts_with($n, '$')) {
            throw new InvalidArgumentException('Argument $n can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->n = $n;
        if (is_string($position) && ! str_starts_with($position, '$')) {
            throw new InvalidArgumentException('Argument $position can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->position = $position;
    }
}
