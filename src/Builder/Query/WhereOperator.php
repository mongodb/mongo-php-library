<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Matches documents that satisfy a JavaScript expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/where/
 */
class WhereOperator implements QueryInterface
{
    public const NAME = '$where';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param non-empty-string $function */
    public string $function;

    /**
     * @param non-empty-string $function
     */
    public function __construct(string $function)
    {
        $this->function = $function;
    }
}
