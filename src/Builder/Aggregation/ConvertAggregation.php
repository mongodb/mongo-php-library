<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Aggregation;

use MongoDB\BSON\Int64;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\ResolvesToInt;
use MongoDB\Builder\Expression\ResolvesToString;
use MongoDB\Builder\Optional;

class ConvertAggregation implements ExpressionInterface
{
    public const NAME = '$convert';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param ExpressionInterface|mixed $input */
    public mixed $input;

    /** @param Int64|ResolvesToInt|ResolvesToString|int|non-empty-string $to */
    public Int64|ResolvesToInt|ResolvesToString|int|string $to;

    /**
     * @param ExpressionInterface|Optional|mixed $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     */
    public mixed $onError;

    /**
     * @param ExpressionInterface|Optional|mixed $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public mixed $onNull;

    /**
     * @param ExpressionInterface|mixed $input
     * @param Int64|ResolvesToInt|ResolvesToString|int|non-empty-string $to
     * @param ExpressionInterface|Optional|mixed $onError The value to return on encountering an error during conversion, including unsupported type conversions. The arguments can be any valid expression.
     * If unspecified, the operation throws an error upon encountering an error and stops.
     * @param ExpressionInterface|Optional|mixed $onNull The value to return if the input is null or missing. The arguments can be any valid expression.
     * If unspecified, $convert returns null if the input is null or missing.
     */
    public function __construct(
        mixed $input,
        Int64|ResolvesToInt|ResolvesToString|int|string $to,
        mixed $onError = Optional::Undefined,
        mixed $onNull = Optional::Undefined,
    ) {
        $this->input = $input;
        $this->to = $to;
        $this->onError = $onError;
        $this->onNull = $onNull;
    }
}
