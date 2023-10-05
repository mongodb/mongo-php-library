<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;

/**
 * Performs a modulo operation on the value of a field and selects documents with a specified result.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/
 */
class ModQuery implements QueryInterface
{
    public const NAME = '$mod';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param Int64|int $divisor */
    public Int64|int $divisor;

    /** @param Int64|int $remainder */
    public Int64|int $remainder;

    /**
     * @param Int64|int $divisor
     * @param Int64|int $remainder
     */
    public function __construct(Int64|int $divisor, Int64|int $remainder)
    {
        $this->divisor = $divisor;
        $this->remainder = $remainder;
    }
}
