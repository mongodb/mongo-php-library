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
 * A ternary operator that evaluates one expression, and depending on the result, returns the value of one of the other two expressions. Accepts either three expressions in an ordered list or three named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/
 */
readonly class CondOperator implements ResolvesToAny
{
    public const NAME = '$cond';
    public const ENCODE = Encode::Object;

    /** @param ResolvesToBool|bool $if */
    public ResolvesToBool|bool $if;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $then */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $else */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else;

    /**
     * @param ResolvesToBool|bool $if
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $then
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $else
     */
    public function __construct(
        ResolvesToBool|bool $if,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else,
    ) {
        $this->if = $if;
        $this->then = $then;
        $this->else = $else;
    }
}
