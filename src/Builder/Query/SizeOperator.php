<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Selects documents if the array field is a specified size.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/size/
 */
class SizeOperator implements QueryInterface
{
    public const NAME = '$size';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param int $value */
    public int $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
