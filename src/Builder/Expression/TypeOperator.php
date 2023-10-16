<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use stdClass;

/**
 * Return the BSON data type of the field.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/type/
 */
readonly class TypeOperator implements ResolvesToString
{
    public const NAME = '$type';
    public const ENCODE = Encode::Single;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public function __construct(Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression)
    {
        $this->expression = $expression;
    }
}
