<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

/**
 * Matches values that are greater than a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/gt/
 */
class GtQuery implements QueryInterface
{
    public const NAME = '$gt';
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
