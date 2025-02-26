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
use MongoDB\Builder\Type\SwitchBranchInterface;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Represents a single case in a $switch expression
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/
 * @internal
 */
final class CaseOperator implements SwitchBranchInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = null;
    public const PROPERTIES = ['case' => 'case', 'then' => 'then'];

    /** @var ResolvesToBool|bool|string $case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here. */
    public readonly ResolvesToBool|bool|string $case;

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then Can be any valid expression. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then;

    /**
     * @param ResolvesToBool|bool|string $case Can be any valid expression that resolves to a boolean. If the result is not a boolean, it is coerced to a boolean value. More information about how MongoDB evaluates expressions as either true or false can be found here.
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $then Can be any valid expression.
     */
    public function __construct(
        ResolvesToBool|bool|string $case,
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $then,
    ) {
        if (is_string($case) && ! str_starts_with($case, '$')) {
            throw new InvalidArgumentException('Argument $case can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->case = $case;
        $this->then = $then;
    }
}
