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
 * Converts an array of key value pairs to a document.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/
 * @internal
 */
final class ArrayToObjectOperator implements ResolvesToObject, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$arrayToObject';
    public const PROPERTIES = ['array' => 'array'];

    /** @var BSONArray|PackedArray|ResolvesToArray|array|string $array */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $array;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $array
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array|string $array)
    {
        if (is_string($array) && ! str_starts_with($array, '$')) {
            throw new InvalidArgumentException('Argument $array can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
    }
}
