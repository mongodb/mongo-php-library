<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Selects documents if a field is of the specified type.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/
 */
class TypeOperator implements FieldQueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var BSONArray|PackedArray|array|int|non-empty-string $type */
    public readonly PackedArray|BSONArray|array|int|string $type;

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

    public function getOperator(): string
    {
        return '$type';
    }
}
