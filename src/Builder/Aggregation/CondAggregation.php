<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToBool;

/**
 * A ternary operator that evaluates one expression, and depending on the result, returns the value of one of the other two expressions. Accepts either three expressions in an ordered list or three named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/
 */
class CondAggregation implements ExpressionInterface
{
    public const NAME = '$cond';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ResolvesToBool|bool $if */
    public ResolvesToBool|bool $if;

    /** @param ExpressionInterface|mixed $then */
    public mixed $then;

    /** @param ExpressionInterface|mixed $else */
    public mixed $else;

    /**
     * @param ResolvesToBool|bool $if
     * @param ExpressionInterface|mixed $then
     * @param ExpressionInterface|mixed $else
     */
    public function __construct(ResolvesToBool|bool $if, mixed $then, mixed $else)
    {
        $this->if = $if;
        $this->then = $then;
        $this->else = $else;
    }
}
