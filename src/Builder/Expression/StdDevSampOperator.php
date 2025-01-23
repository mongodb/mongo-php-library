<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;

use function array_is_list;

/**
 * Calculates the sample standard deviation of the input values. Use if the values encompass a sample of a population of data from which to generalize about the population. $stdDevSamp ignores non-numeric values.
 * If the values represent the entire population of data or you do not wish to generalize about a larger population, use $stdDevPop instead.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/
 * @internal
 */
final class StdDevSampOperator implements ResolvesToDouble, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$stdDevSamp';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var list<Decimal128|Int64|ResolvesToNumber|float|int|string> $expression */
    public readonly array $expression;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string ...$expression
     * @no-named-arguments
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string ...$expression)
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
