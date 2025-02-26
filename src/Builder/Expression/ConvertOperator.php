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
use stdClass;

/**
 * Converts a value to a specified type.
 * New in MongoDB 4.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/
 * @internal
 */
final class ConvertOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$convert';
    public const PROPERTIES = ['input' => 'input', 'to' => 'to', 'onError' => 'onError', 'onNull' => 'onNull'];

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
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input
     * @param ResolvesToInt|ResolvesToString|int|string $to
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|ResolvesToString|int|string $to,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->to = $to;
        $this->onError = $onError;
        $this->onNull = $onNull;
    }
}
