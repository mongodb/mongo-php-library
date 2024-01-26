<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Returns true if the first value is greater than or equal to the second.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gte/
 */
class GteOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Array;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1 */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2 */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression1
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression2
     */
    public function __construct(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression1,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression2,
    ) {
        $this->expression1 = $expression1;
        $this->expression2 = $expression2;
    }

    public function getOperator(): string
    {
        return '$gte';
    }
}
