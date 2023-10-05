<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

/**
 * Matches values that are less than or equal to a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/lte/
 */
class LteQuery implements QueryInterface
{
    public const NAME = '$lte';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param mixed $value */
    public mixed $value;

    /**
     * @param mixed $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }
}
