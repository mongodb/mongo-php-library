<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;
use stdClass;

/**
 * Matches all values that are not equal to a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/ne/
 */
readonly class NeOperator implements FieldQueryInterface
{
    public const NAME = '$ne';
    public const ENCODE = Encode::Single;

    /** @param Type|array|bool|float|int|non-empty-string|null|stdClass $value */
    public Type|stdClass|array|bool|float|int|null|string $value;

    /**
     * @param Type|array|bool|float|int|non-empty-string|null|stdClass $value
     */
    public function __construct(Type|stdClass|array|bool|float|int|null|string $value)
    {
        $this->value = $value;
    }
}
