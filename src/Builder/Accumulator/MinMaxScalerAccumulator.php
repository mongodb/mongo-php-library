<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Int64;
use MongoDB\Builder\Expression\ResolvesToNumber;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Exception\InvalidArgumentException;

use function is_string;
use function str_starts_with;

/**
 * Normalizes a numeric expression within a window of values. By default, values can range
 * between zero and one. The smallest value becomes zero, the largest value becomes one, and
 * all other values scale proportionally in between. You can also specify a custom minimum
 * and maximum value for the normalized output range.
 * Available only in the $setWindowFields stage.
 *
 * New in MongoDB 8.2
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minMaxScaler/
 * @internal
 */
final class MinMaxScalerAccumulator implements WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$minMaxScaler';
    public const PROPERTIES = ['input' => 'input', 'min' => 'min', 'max' => 'max'];

    /** @var Decimal128|Int64|ResolvesToNumber|float|int|string $input Numeric expression containing the value that you want to normalize. */
    public readonly Decimal128|Int64|ResolvesToNumber|float|int|string $input;

    /** @var Optional|Decimal128|Int64|float|int $min Minimum value that you want in the output. If omitted, defaults to 0. */
    public readonly Optional|Decimal128|Int64|float|int $min;

    /** @var Optional|Decimal128|Int64|float|int $max Maximum value that you want in the output. If omitted, defaults to 1. */
    public readonly Optional|Decimal128|Int64|float|int $max;

    /**
     * @param Decimal128|Int64|ResolvesToNumber|float|int|string $input Numeric expression containing the value that you want to normalize.
     * @param Optional|Decimal128|Int64|float|int $min Minimum value that you want in the output. If omitted, defaults to 0.
     * @param Optional|Decimal128|Int64|float|int $max Maximum value that you want in the output. If omitted, defaults to 1.
     */
    public function __construct(
        Decimal128|Int64|ResolvesToNumber|float|int|string $input,
        Optional|Decimal128|Int64|float|int $min = Optional::Undefined,
        Optional|Decimal128|Int64|float|int $max = Optional::Undefined,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->input = $input;
        $this->min = $min;
        $this->max = $max;
    }
}
