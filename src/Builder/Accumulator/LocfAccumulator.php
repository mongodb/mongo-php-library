<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Accumulator;

use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\WindowInterface;
use stdClass;

/**
 * Last observation carried forward. Sets values for null and missing fields in a window to the last non-null value for the field.
 * Available in the $setWindowFields stage.
 * New in MongoDB 5.2.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/locf/
 */
class LocfAccumulator implements WindowInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression */
    public readonly Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $expression
     */
    public function __construct(Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression)
    {
        $this->expression = $expression;
    }

    public function getOperator(): string
    {
        return '$locf';
    }
}
