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

use function array_is_list;

/**
 * Returns true when any of its expressions evaluates to true. Accepts any number of argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/or/
 * @internal
 */
final class OrOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$or';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var list<DateTimeInterface|ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string> $expression */
    public readonly array $expression;

    /**
     * @param DateTimeInterface|ExpressionInterface|ResolvesToBool|Type|array|bool|float|int|null|stdClass|string ...$expression
     * @no-named-arguments
     */
    public function __construct(
        DateTimeInterface|Type|ResolvesToBool|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression,
    ) {
        if (\count($expression) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }

        $this->expression = $expression;
    }
}
