<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns a set with elements that appear in the first set but not in the second set; i.e. performs a relative complement of the second set relative to the first. Accepts exactly two argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setDifference/
 * @internal
 */
final class SetDifferenceOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$setDifference';
    public const PROPERTIES = ['expression1' => 'expression1', 'expression2' => 'expression2'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $expression1 The arguments can be any valid expression as long as they each resolve to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $expression1;

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $expression2 The arguments can be any valid expression as long as they each resolve to an array. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $expression2;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $expression1 The arguments can be any valid expression as long as they each resolve to an array.
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $expression2 The arguments can be any valid expression as long as they each resolve to an array.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $expression1,
        PackedArray|ResolvesToArray|BSONArray|array|string $expression2,
    ) {
        if (is_string($expression1) && ! str_starts_with($expression1, '$')) {
            throw new InvalidArgumentException('Argument $expression1 can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($expression1) && ! array_is_list($expression1)) {
            throw new InvalidArgumentException('Expected $expression1 argument to be a list, got an associative array.');
        }

        $this->expression1 = $expression1;
        if (is_string($expression2) && ! str_starts_with($expression2, '$')) {
            throw new InvalidArgumentException('Argument $expression2 can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($expression2) && ! array_is_list($expression2)) {
            throw new InvalidArgumentException('Expected $expression2 argument to be a list, got an associative array.');
        }

        $this->expression2 = $expression2;
    }
}
