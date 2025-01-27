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
 * Returns the element at the specified array index.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/
 * @internal
 */
final class ArrayElemAtOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$arrayElemAt';
    public const PROPERTIES = ['array' => 'array', 'idx' => 'idx'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $array */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $array;

    /** @var ResolvesToInt|int|string $idx */
    public readonly ResolvesToInt|int|string $idx;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $array
     * @param ResolvesToInt|int|string $idx
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $array,
        ResolvesToInt|int|string $idx,
    ) {
        if (is_string($array) && ! str_starts_with($array, '$')) {
            throw new InvalidArgumentException('Argument $array can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
        if (is_string($idx) && ! str_starts_with($idx, '$')) {
            throw new InvalidArgumentException('Argument $idx can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->idx = $idx;
    }
}
