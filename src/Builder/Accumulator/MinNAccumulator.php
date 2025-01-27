<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns the n smallest values in an array. Distinct from the $minN accumulator.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/
 * @internal
 */
final class MinNAccumulator implements AccumulatorInterface, WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$minN';
    public const PROPERTIES = ['input' => 'input', 'n' => 'n'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to the array from which to return the maximal n elements. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $input;

    /** @var ResolvesToInt|int|string $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns. */
    public readonly ResolvesToInt|int|string $n;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $input An expression that resolves to the array from which to return the maximal n elements.
     * @param ResolvesToInt|int|string $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $maxN returns.
     */
    public function __construct(
        PackedArray|ResolvesToArray|BSONArray|array|string $input,
        ResolvesToInt|int|string $n,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
        if (is_string($n) && ! str_starts_with($n, '$')) {
            throw new InvalidArgumentException('Argument $n can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->n = $n;
    }
}
