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
 * A ternary operator that evaluates one expression, and depending on the result, returns the value of one of the other two expressions. Accepts either three expressions in an ordered list or three named parameters.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/
 * @internal
 */
final class CondOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$cond';
    public const PROPERTIES = ['if' => 'if', 'then' => 'then', 'else' => 'else'];

    /** @var ResolvesToBool|bool|string $if */
    public readonly ResolvesToBool|bool|string $if;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $else */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else;

    /**
     * @param ResolvesToBool|bool|string $if
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $else
     */
    public function __construct(
        ResolvesToBool|bool|string $if,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then,
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else,
    ) {
        $this->if = $if;
        $this->then = $then;
        $this->else = $else;
    }
}
