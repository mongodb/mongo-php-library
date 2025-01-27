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
 * Returns a specified number of elements from the beginning of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN-array-element/
 * @internal
 */
final class FirstNOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$firstN';
    public const PROPERTIES = ['n' => 'n', 'input' => 'input'];

    /** @var ResolvesToInt|int|string $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns. */
    public readonly ResolvesToInt|int|string $n;

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to the array from which to return n elements. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /**
     * @param ResolvesToInt|int|string $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to the array from which to return n elements.
     */
    public function __construct(
        ResolvesToInt|int|string $n,
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
    ) {
        if (is_string($n) && ! str_starts_with($n, '$')) {
            throw new InvalidArgumentException('Argument $n can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->n = $n;
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
    }
}
