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

/**
 * Returns a specified number of elements from the beginning of an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN-array-element/
 */
class FirstNOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns. */
    public readonly ResolvesToInt|int $n;

    /** @var BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements. */
    public readonly PackedArray|ResolvesToArray|BSONArray|array $input;

    /**
     * @param ResolvesToInt|int $n An expression that resolves to a positive integer. The integer specifies the number of array elements that $firstN returns.
     * @param BSONArray|PackedArray|ResolvesToArray|array $input An expression that resolves to the array from which to return n elements.
     */
    public function __construct(ResolvesToInt|int $n, PackedArray|ResolvesToArray|BSONArray|array $input)
    {
        $this->n = $n;
        if (is_array($input) && ! array_is_list($input)) {
            throw new InvalidArgumentException('Expected $input argument to be a list, got an associative array.');
        }

        $this->input = $input;
    }

    public function getOperator(): string
    {
        return '$firstN';
    }
}
