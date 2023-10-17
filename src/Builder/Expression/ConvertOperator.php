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
use MongoDB\Builder\Type\Optional;
use stdClass;

/**
 * Converts a value to a specified type.
 * New in MongoDB 4.0.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/
 */
class ConvertOperator implements ResolvesToAny, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $input */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /** @var ResolvesToInt|ResolvesToString|int|non-empty-string $to */
    public readonly ResolvesToInt|ResolvesToString|int|string $to;

    /**
     * @var Optional|ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     */
    public readonly Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @var Optional|ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public readonly Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $input
     * @param ResolvesToInt|ResolvesToString|int|non-empty-string $to
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param Optional|ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public function __construct(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        ResolvesToInt|ResolvesToString|int|string $to,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
        Optional|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onNull = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->to = $to;
        $this->onError = $onError;
        $this->onNull = $onNull;
    }

    public function getOperator(): string
    {
        return '$convert';
    }
}
