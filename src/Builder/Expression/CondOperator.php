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
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

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

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $else */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else;

    /**
     * @param ResolvesToBool|bool|string $if
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $else
     */
    public function __construct(
        ResolvesToBool|bool|string $if,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $else,
    ) {
        if (is_string($if) && ! str_starts_with($if, '$')) {
            throw new InvalidArgumentException('Argument $if can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->if = $if;
        $this->then = $then;
        $this->else = $else;
    }
}
