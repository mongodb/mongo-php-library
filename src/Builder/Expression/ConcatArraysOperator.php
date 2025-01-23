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

/**
 * Concatenates arrays to return the concatenated array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/
 * @internal
 */
final class ConcatArraysOperator implements ResolvesToArray, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$concatArrays';
    public const PROPERTIES = ['array' => 'array'];

    /** @var list<BSONArray|PackedArray|ResolvesToArray|array|string> $array */
    public readonly array $array;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string ...$array
     * @no-named-arguments
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array|string ...$array)
    {
        if (\count($array) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $array, got %d.', 1, \count($array)));
        }

        if (! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array arguments to be a list (array), named arguments are not supported');
        }

        $this->array = $array;
    }
}
