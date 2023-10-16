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
 * Returns the boolean value that is the opposite of its argument expression. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/
 */
readonly class NotOperator implements ResolvesToBool
{
    public const NAME = '$not';
    public const ENCODE = Encode::Single;

    /** @param ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|non-empty-string|null|stdClass $expression */
    public Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|non-empty-string|null|stdClass $expression
     */
    public function __construct(
        Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ) {
        $this->expression = $expression;
    }
}
