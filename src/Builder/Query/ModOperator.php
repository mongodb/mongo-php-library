<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Performs a modulo operation on the value of a field and selects documents with a specified result.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/
 */
class ModOperator implements QueryInterface
{
    public const NAME = '$mod';
    public const ENCODE = \MongoDB\Builder\Encode::Array;

    /** @param int $divisor */
    public int $divisor;

    /** @param int $remainder */
    public int $remainder;

    /**
     * @param int $divisor
     * @param int $remainder
     */
    public function __construct(int $divisor, int $remainder)
    {
        $this->divisor = $divisor;
        $this->remainder = $remainder;
    }
}
