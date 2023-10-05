<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;

/**
 * Matches none of the values specified in an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/
 */
class NinQuery implements QueryInterface
{
    public const NAME = '$nin';
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
