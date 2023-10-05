<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

/**
 * Last observation carried forward. Sets values for null and missing fields in a window to the last non-null value for the field.
 * Available in the $setWindowFields stage.
 * New in version 5.2.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/locf/
 */
class LocfAggregation implements ExpressionInterface
{
    public const NAME = '$locf';
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
