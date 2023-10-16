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
 * Matches values that are less than or equal to a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/lte/
 */
readonly class LteOperator implements FieldQueryInterface
{
    public const NAME = '$lte';
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
