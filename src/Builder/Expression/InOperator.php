<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Expression;

use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * Returns a boolean indicating whether a specified value is in an array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/in/
 */
readonly class InOperator implements ResolvesToBool
{
    public const NAME = '$in';
    public const ENCODE = Encode::Array;

    /** @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression Any valid expression expression. */
    public Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression;

    /** @param BSONArray|PackedArray|ResolvesToArray|array $array Any valid expression that resolves to an array. */
    public PackedArray|ResolvesToArray|BSONArray|array $array;

    /**
     * @param ExpressionInterface|Type|array|bool|float|int|non-empty-string|null|stdClass $expression Any valid expression expression.
     * @param BSONArray|PackedArray|ResolvesToArray|array $array Any valid expression that resolves to an array.
     */
    public function __construct(
        Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $expression,
        PackedArray|ResolvesToArray|BSONArray|array $array,
    ) {
        $this->expression = $expression;
        if (is_array($array) && ! array_is_list($array)) {
            throw new InvalidArgumentException('Expected $array argument to be a list, got an associative array.');
        }

        $this->array = $array;
    }
}
