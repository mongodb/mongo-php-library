<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Selects documents if a field is of the specified type.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/
 */
readonly class TypeOperator implements FieldQueryInterface
{
    public const NAME = '$type';
    public const ENCODE = Encode::Single;

    /** @param BSONArray|PackedArray|array|int|non-empty-string $type */
    public PackedArray|BSONArray|array|int|string $type;

    /**
     * @param BSONArray|PackedArray|array|int|non-empty-string $type
     */
    public function __construct(PackedArray|BSONArray|array|int|string $type)
    {
        if (is_array($type) && ! array_is_list($type)) {
            throw new InvalidArgumentException('Expected $type argument to be a list, got an associative array.');
        }

        $this->type = $type;
    }
}
