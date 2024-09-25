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
 * Returns the boolean value that is the opposite of its argument expression. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/
 */
class NotOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Array;

    /** @var ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string $expression */
    public readonly Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function __construct(
        Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ) {
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$not';
    }
}
