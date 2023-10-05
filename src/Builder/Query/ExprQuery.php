<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Allows use of aggregation expressions within the query language.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/
 */
class ExprQuery implements QueryInterface
{
    public const NAME = '$expr';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param ExpressionInterface|mixed $expression */
    public mixed $expression;

    /**
     * @param ExpressionInterface|mixed $expression
     */
    public function __construct(mixed $expression)
    {
        $this->expression = $expression;
    }
}
