<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Query;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\FieldQueryInterface;
use stdClass;

/**
 * Allows use of aggregation expressions within the query language.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/
 */
readonly class ExprOperator implements FieldQueryInterface
{
    public const NAME = '$expr';
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
