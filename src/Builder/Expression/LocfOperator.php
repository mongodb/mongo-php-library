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
 * Last observation carried forward. Sets values for null and missing fields in a window to the last non-null value for the field.
 * Available in the $setWindowFields stage.
 * New in version 5.2.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/locf/
 */
readonly class LocfOperator implements ResolvesToAny
{
    public const NAME = '$locf';
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
