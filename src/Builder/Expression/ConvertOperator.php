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
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Converts a value to a specified type. Any type can be converted to string.
 * If the optional base argument is specified, $convert interprets the input string as a
 * number in the given base and converts it to a decimal, or converts a numeric value to a
 * string representation in that base. Supported bases are 2 (binary), 8 (octal), 10
 * (decimal), and 16 (hexadecimal).
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/
 * @internal
 */
final class ConvertOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$convert';

    public const PROPERTIES = [
        'input' => 'input',
        'to' => 'to',
        'onError' => 'onError',
        'onNull' => 'onNull',
        'base' => 'base',
    ];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /** @var ResolvesToInt|ResolvesToString|int|string $to */
    public readonly ResolvesToInt|ResolvesToString|int|string $to;

    /**
     * @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull;

    /**
     * @var Optional|ResolvesToInt|int|string $base The numeric base to use when converting between strings and integers. Must be one of
     * 2 (binary), 8 (octal), 10 (decimal), or 16 (hexadecimal).
     * When converting a string to a number, $convert interprets the string as a number in
     * the given base and returns the decimal equivalent.
     * When converting a number to a string, $convert returns the string representation of
     * the number in the given base.
     * If unspecified, $convert uses base 10.
     *
     * New in MongoDB 8.3
     */
    public readonly Optional|ResolvesToInt|int|string $base;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input
     * @param ResolvesToInt|ResolvesToString|int|string $to
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     * @param Optional|ResolvesToInt|int|string $base The numeric base to use when converting between strings and integers. Must be one of
     * 2 (binary), 8 (octal), 10 (decimal), or 16 (hexadecimal).
     * When converting a string to a number, $convert interprets the string as a number in
     * the given base and returns the decimal equivalent.
     * When converting a number to a string, $convert returns the string representation of
     * the number in the given base.
     * If unspecified, $convert uses base 10.
     *
     * New in MongoDB 8.3
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|ResolvesToString|int|string $to,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
        Optional|ResolvesToInt|int|string $base = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->to = $to;
        $this->onError = $onError;
        $this->onNull = $onNull;
        if (is_string($base) && ! str_starts_with($base, '$')) {
            throw new InvalidArgumentException('Argument $base can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->base = $base;
    }
}
