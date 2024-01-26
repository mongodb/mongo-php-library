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
 * Returns boolean true if the specified expression resolves to an integer, decimal, double, or long.
 * Returns boolean false if the expression resolves to any other BSON type, null, or a missing field.
 * New in MongoDB 4.4.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/
 */
class IsNumberOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function __construct(Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression)
    {
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$isNumber';
    }
}
