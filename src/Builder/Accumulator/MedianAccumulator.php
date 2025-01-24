<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Returns an approximation of the median, the 50th percentile, as a scalar value.
 * New in MongoDB 7.0.
 * This operator is available as an accumulator in these stages:
 * $group
 * $setWindowFields
 * It is also available as an aggregation expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/
 * @internal
 */
final class MedianAccumulator implements AccumulatorInterface, WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$median';
    public const PROPERTIES = ['input' => 'input', 'method' => 'method'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $input;

    /** @var string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'. */
    public readonly string $method;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $input $median calculates the 50th percentile value of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $median calculation ignores it.
     * @param string $method The method that mongod uses to calculate the 50th percentile value. The method must be 'approximate'.
     */
    public function __construct(Decimal128|Int64|ResolvesToNumber|float|int|string $input, string $method)
    {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->input = $input;
        $this->method = $method;
    }
}
