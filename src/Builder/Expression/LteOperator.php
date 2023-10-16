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
 * Returns true if the first value is less than or equal to the second.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lte/
 */
readonly class LteOperator implements ResolvesToBool
{
    public const NAME = '$lte';
    public const ENCODE = Encode::Array;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression1 */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression2 */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression2
     */
    public function __construct(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }
}
