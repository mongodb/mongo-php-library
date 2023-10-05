<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Groups incoming documents based on the value of a specified expression, then computes the count of documents in each distinct group.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortByCount/
 */
class SortByCountStage implements StageInterface
{
    public const NAME = '$sortByCount';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

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
