<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Concatenates any number of strings.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concat/
 */
class ConcatOperator implements ResolvesToString, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var list<ResolvesToString|non-empty-string> $expression */
    public readonly array $expression;

    /**
     * @param ResolvesToString|non-empty-string ...$expression
     * @no-named-arguments
     */
    public function __construct(ResolvesToString|string ...$expression)
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
        return '$concat';
    }
}
