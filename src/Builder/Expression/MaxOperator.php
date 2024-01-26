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
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;

/**
 * Returns the maximum value that results from applying an expression to each document.
 * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/
 */
class MaxOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<ExpressionInterface|Type|array|bool|float|int|null|stdClass|string> $expression */
    public readonly array $expression;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string ...$expression
     * @no-named-arguments
     */
    public function __construct(Type|ExpressionInterface|stdClass|array|bool|float|int|null|string ...$expression)
    {
        if (\count($expression) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }

        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$max';
    }
}
