<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\FieldQueryInterface;

/**
 * Performs a modulo operation on the value of a field and selects documents with a specified result.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/
 */
readonly class ModOperator implements FieldQueryInterface
{
    public const NAME = '$mod';
    public const ENCODE = Encode::Array;

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
