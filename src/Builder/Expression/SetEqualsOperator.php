<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;

/**
 * Returns true if the input sets have the same distinct elements. Accepts two or more argument expressions.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setEquals/
 * @internal
 */
final class SetEqualsOperator implements ResolvesToBool, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$setEquals';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var list<BSONArray|PackedArray|ResolvesToArray|array|string> $expression */
    public readonly array $expression;

    /**
     * @param BSONArray|PackedArray|ResolvesToArray|array|string ...$expression
     * @no-named-arguments
     */
    public function __construct(PackedArray|ResolvesToArray|BSONArray|array|string ...$expression)
    {
        if (\count($expression) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $expression, got %d.', 1, \count($expression)));
        }

        if (! array_is_list($expression)) {
            throw new InvalidArgumentException('Expected $expression arguments to be a list (array), named arguments are not supported');
        }

        $this->expression = $expression;
    }
}
