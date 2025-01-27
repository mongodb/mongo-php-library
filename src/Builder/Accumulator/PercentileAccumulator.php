<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Expression\ResolvesToArray;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;
use function is_string;
use function str_starts_with;

/**
 * Returns an array of scalar values that correspond to specified percentile values.
 * New in MongoDB 7.0.
 *
 * This operator is available as an accumulator in these stages:
 * $group
 *
 * $setWindowFields
 *
 * It is also available as an aggregation expression.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/
 * @internal
 */
final class PercentileAccumulator implements AccumulatorInterface, WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$percentile';
    public const PROPERTIES = ['input' => 'input', 'p' => 'p', 'method' => 'method'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $input;

    /**
     * @var BSONArray|PackedArray|ResolvesToArray|array|string $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     */
    public readonly PackedArray|ResolvesToArray|BSONArray|array|string $p;

    /** @var string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'. */
    public readonly string $method;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $input $percentile calculates the percentile values of this data. input must be a field name or an expression that evaluates to a numeric type. If the expression cannot be converted to a numeric type, the $percentile calculation ignores it.
     * @param BSONArray|PackedArray|ResolvesToArray|array|string $p $percentile calculates a percentile value for each element in p. The elements represent percentages and must evaluate to numeric values in the range 0.0 to 1.0, inclusive.
     * $percentile returns results in the same order as the elements in p.
     * @param string $method The method that mongod uses to calculate the percentile value. The method must be 'approximate'.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $input,
        PackedArray|ResolvesToArray|BSONArray|array|string $p,
        string $method,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->input = $input;
        if (is_string($p) && ! str_starts_with($p, '$')) {
            throw new InvalidArgumentException('Argument $p can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        if (is_array($p) && ! array_is_list($p)) {
            throw new InvalidArgumentException('Expected $p argument to be a list, got an associative array.');
        }

        $this->p = $p;
        $this->method = $method;
    }
}
