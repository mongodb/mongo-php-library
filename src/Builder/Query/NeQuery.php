<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

/**
 * Matches all values that are not equal to a specified value.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/ne/
 */
class NeQuery implements QueryInterface
{
    public const NAME = '$ne';
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
