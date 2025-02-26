<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use stdClass;

/**
 * Returns the boolean value that is the opposite of its argument expression. Accepts a single argument expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/
 * @internal
 */
final class NotOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Array;
    public const NAME = '$not';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var DateTimeInterface|ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string $expression */
    public readonly DateTimeInterface|Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param DateTimeInterface|ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function __construct(
        DateTimeInterface|Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
    ) {
        $this->expression = $expression;
    }
}
