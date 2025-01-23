<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;

/**
 * Returns a sum of numerical values. Ignores non-numeric values.
 * Changed in MongoDB 5.0: Available in the $setWindowFields stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/
 * @internal
 */
final class SumOperator implements ResolvesToNumber, OperatorInterface
{
    public const ENCODE = Encode::Single;
    public const NAME = '$sum';
    public const PROPERTIES = ['expression' => 'expression'];

    /** @var list<BSONArray|Decimal128|Int64|PackedArray|ResolvesToArray|ResolvesToNumber|array|float|int|string> $expression */
    public readonly array $expression;

    /**
     * @param BSONArray|Decimal128|Int64|PackedArray|ResolvesToArray|ResolvesToNumber|array|float|int|string ...$expression
     * @no-named-arguments
     */
    public function __construct(
        Decimal128|Int64|PackedArray|ResolvesToArray|ResolvesToNumber|BSONArray|array|float|int|string ...$expression,
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
