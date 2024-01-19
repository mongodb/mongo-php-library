<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Computes and returns the hash value of the input expression using the same hash function that MongoDB uses to create a hashed index. A hash function maps a key or string to a fixed-size numeric value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toHashedIndexKey/
 */
class ToHashedIndexKeyOperator implements ResolvesToLong, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $value key or string to hash */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $value key or string to hash
     */
    public function __construct(Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $value)
    {
        $this->value = $value;
    }

    public function getOperator(): string
    {
        return '$toHashedIndexKey';
    }
}
